<?php

namespace Wenwen\FrontendBundle\Model\API;

final class HttpStatus
{
    // Don't add more status code, we only use this many statuses for our restful api
    const HTTP_OK = 200; // Success (GET, PUT, PATCH)
    const HTTP_CREATED = 201; // Created (POST)
    const HTTP_NO_CONTENT = 204; // No content (DELETE)
    const HTTP_BAD_REQUEST = 400; // Validation error
    const HTTP_UNAUTHORIZED = 401; // Authentication error
    const HTTP_FORBIDDEN = 403; // Forbidden (user that only with given permissions can access)
    const HTTP_NOT_FOUND = 404; // Not found
    const HTTP_INTERNAL_SERVER_ERROR = 500; //Unexpected error
}
