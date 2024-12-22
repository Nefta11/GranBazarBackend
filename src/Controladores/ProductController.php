<?php

namespace App\Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\Product;
use App\Modelos\Category; // Asegúrate de que esta ruta sea correcta

class ProductController
{

    public function create(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());

        if (!$parametros) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Obtener el ID de la categoría a partir del nombre
        $category = Category::where('name', $parametros->category)->first();
        if (!$category) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Categoría no válida.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        try {
            $product = new Product();
            $product->name = $parametros->name;
            $product->description = $parametros->description;
            $product->price = $parametros->price;
            $product->category_id = $category->id;
            $product->image = $parametros->image;
            $product->status = $parametros->status;
            $product->save();

            $res->getBody()->write(json_encode(['success' => true, 'message' => 'Producto creado exitosamente.']));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error al crear producto.']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }

    public function getAll(Request $req, Response $res, $args)
    {
        $products = Product::all();
        $res->getBody()->write(json_encode(['success' => true, 'data' => $products]));
        return $res->withHeader('Content-type', 'application/json');
    }

    public function update(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());
        $product = Product::find($args['id']);

        if (!$product) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Producto no encontrado.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Obtener el ID de la categoría a partir del nombre
        $category = Category::where('name', $parametros->category)->first();
        if (!$category) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Categoría no válida.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        try {
            $product->name = $parametros->name;
            $product->description = $parametros->description;
            $product->price = $parametros->price;
            $product->category_id = $category->id;
            $product->image = $parametros->image;
            $product->status = $parametros->status;
            $product->save();

            $res->getBody()->write(json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente.']));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error al actualizar producto.']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }

    /**
     * Eliminar un producto.
     * 
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function delete(Request $req, Response $res, $args)
    {
        $product = Product::find($args['id']);

        if (!$product) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Producto no encontrado.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        try {
            $product->delete();
            $res->getBody()->write(json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente.']));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error al eliminar producto.']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }
}
