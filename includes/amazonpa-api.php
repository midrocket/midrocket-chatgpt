<?php

// https://webservices.amazon.com/paapi5/documentation/quick-start/using-sdk.html
// https://github.com/thewirecutter/paapi5-php-sdk?tab=readme-ov-file

require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use GuzzleHttp\Client;

function amazon_pa_api_search_products($keyword) {
    $config = new Configuration();

    // Credentials from options
    $options = get_option('midrocket_chatbot_gpt_options');
    if(empty($options['amazon_access_key']) || empty($options['amazon_secret_key']) ){
        return;
    }

    $config->setAccessKey($options['amazon_access_key']);
    $config->setSecretKey($options['amazon_secret_key']);
    $config->setHost('webservices.amazon.es');
    $config->setRegion('eu-west-1');

    $partnerTag = $options['amazon_partner_tag'];

    $apiInstance = new DefaultApi(new Client(), $config);

    $searchIndex = "All"; // Category
    $itemCount = 3; // Results

    $resources = [
        SearchItemsResource::ITEM_INFOTITLE,
        SearchItemsResource::OFFERSLISTINGSPRICE,
        SearchItemsResource::ITEM_INFOTITLE,
        SearchItemsResource::BROWSE_NODE_INFOBROWSE_NODES,
        SearchItemsResource::IMAGESPRIMARYMEDIUM
    ];

    $searchItemsRequest = new SearchItemsRequest();
    $searchItemsRequest->setSearchIndex($searchIndex);
    $searchItemsRequest->setKeywords($keyword);
    $searchItemsRequest->setItemCount($itemCount);
    $searchItemsRequest->setPartnerTag($partnerTag);
    $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
    $searchItemsRequest->setResources($resources);

    $products = [];

    try {
        $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

        if ($searchItemsResponse->getSearchResult() !== null) {
            foreach ($searchItemsResponse->getSearchResult()->getItems() as $item) {
                $product = array();

                if ($item->getItemInfo() && $item->getItemInfo()->getTitle() && $item->getItemInfo()->getTitle()->getDisplayValue()) {
                    $product['product_name'] = $item->getItemInfo()->getTitle()->getDisplayValue();
                }

                if ($item->getOffers() && $item->getOffers()->getListings() && $item->getOffers()->getListings()[0]->getPrice() && $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount()) {
                    $product['product_price'] = $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount();
                }

                $product['product_url'] = $item->getDetailPageURL();

                if ($item->getImages() && $item->getImages()->getPrimary() && $item->getImages()->getPrimary()->getMedium()) {
                    $product['product_picture'] = $item->getImages()->getPrimary()->getMedium()->getURL();
                }

                $products[] = $product;
            }
        }
    } catch (ApiException $exception) {
        echo "Error calling PA-API 5.0" . PHP_EOL;
        echo "HTTP Status Code: " . $exception->getCode() . PHP_EOL;
        echo "Error Message: " . $exception->getMessage() . PHP_EOL;
    } catch (Exception $exception) {
        echo "Error Message: " . $exception->getMessage() . PHP_EOL;
    }

    return $products;
}

// Filters ChatbotGPT
function modify_chatgpt_rules_prompt($rules_prompt) {
    // Include specific amazon rules such as:
    // $append_rules = 'Regla adicional muy importante: Cuando un usuario pregunte por un producto, trataremos de averigurar cuál es la keyword que utilizaremos en la
    //                API de Amazon. Si no está claro, le haremos más preguntas hasta que nos ofrezca suficiente información para obtener
    //                una keyword. Una vez identifiques una keyword, la respuesta que darás sólo contendrá la keyword en formato json. 
    //                Ejemplo: {"keyword": "taza de cafe"}';
    
    $append_rules = "\n\nEs crucial que, al responder a un usuario que pregunta por un producto, nuestro objetivo principal sea identificar la keyword adecuada para utilizar en la API de Amazon. La keyword identificada no debe incluir atributos relacionados con el precio, como \"barato\" o \"caro\". Si la información proporcionada por el usuario no es suficiente para determinar una keyword clara, debemos hacer preguntas adicionales hasta obtener los detalles necesarios. Una vez que se identifique una keyword, tu respuesta debe ser exclusivamente esa keyword, presentada en formato JSON. No se debe proporcionar ninguna otra información o respuesta que no sea la keyword o preguntas dirigidas a identificarla. Ejemplo correcto de respuesta: {\"keyword\": \"limpiador de piscinas\"} Si el usuario no proporciona suficiente información inicialmente, debes hacer preguntas específicas hasta que puedas identificar una keyword adecuada, sin desviarte del objetivo de proporcionar una respuesta en el formato JSON especificado.";
    $rules_prompt .= $append_rules;
    
    return $rules_prompt;
}
add_filter('chatbotgpt_filter_rules_prompt', 'modify_chatgpt_rules_prompt', 10, 1);

function check_chatgpt_response_for_keyword($response) {
    $content = $response['choices'][0]['message']['content'];
    preg_match('/\{"keyword": "([^"]+)"\}/', $content, $matches);

    // Verify if answer has specific "keyword" and modify answer
    if (isset($response['choices'][0]['message']['content']) && !empty($matches)) {
        // $content = json_decode($response['choices'][0]['message']['content'], true);
        $keyword_json = json_decode($matches[0], true);

        if (json_last_error() === JSON_ERROR_NONE && isset($keyword_json['keyword'])) {
            $keyword = $keyword_json['keyword'];

            // Amazon API Call
            $products = amazon_pa_api_search_products($keyword);

            if(!empty($products)){
                $products_response .= '<ul class="product-list">';
                foreach($products as $product){
                    $products_response .= '<li class="product-item">
                                            <a href="'.$product['product_url'].'" target="_blank">
                                                <div class="product-image"><img src="'.$product['product_picture'].'"></div>
                                                <div class="product-card">
                                                    <div class="product-details">
                                                        <span class="product-name">'.$product['product_name'].'</span>
                                                        <span class="product-price">'.$product['product_price'].'</span>
                                                    </div>
                                                    <div class="product-action">
                                                        <i class="fi fi-br-angle-right"></i>
                                                    </div>
                                                </div>
                                            </a>
                                           </li>';
                }
                $products_response .= '</ul>';
            }
            $response['choices'][0]['message']['content'] = $products_response;
        }
    }
    return $response;
}
add_filter('chatbotgpt_filter_response_before', 'check_chatgpt_response_for_keyword', 10, 1);
