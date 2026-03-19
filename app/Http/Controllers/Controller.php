<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API Back2 Newsletters",
    version: "1.0.0",
    description: "Documentation Swagger pour la gestion des newsletters et archives."
)]
#[OA\Server(
    url: "http://localhost:8001/api",
    description: "Serveur Local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    name: "Authorization",
    in: "header"
)]
abstract class Controller
{
    //
}
