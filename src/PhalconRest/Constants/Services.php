<?php

namespace PhalconRest\Constants;

class Services
{
    // Phalcon
    const DISPATCHER = "dispatcher";
    const ROUTER = "router";
    const URL = "url";
    const REQUEST = "request";
    const RESPONSE = "response";
    const COOKIES = "cookies";
    const FILTER = "filter";
    const FLASH = "flash";
    const FLASH_SESSION = "flashSession";
    const SESSION = "session";
    const EVENTS_MANAGER = "eventsManager";
    const DB = "db";
    const SECURITY = "security";
    const CRYPT = "crypt";
    const TAG = "tag";
    const ESCAPER = "escaper";
    const ANNOTATIONS = "annotations";
    const MODELS_MANAGER = "modelsManager";
    const MODELS_METADATA = "modelsMetadata";
    const TRANSACTION_MANAGER = "transactionManager";
    const MODELS_CACHE = "modelsCache";
    const VIEWS_CACHE = "viewsCache";
    const ASSETS = "assets";

    // PhalconRest
    const AUTH_MANAGER = 'authManager';
    const FRACTAL_MANAGER = 'fractalManager';
    const TOKEN_PARSER = 'tokenParser';
    const API_SERVICE = 'apiService';
    const QUERY = 'query';
    const PHQL_QUERY_PARSER = 'phqlQueryParser';
    const URL_QUERY_PARSER = 'urlQueryParser';
}
