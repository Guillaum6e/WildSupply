<?php

namespace App\Controller;

use App\Model\ProductManager;

class ProductController extends AbstractController
{
    /**
     * The list of products
     *
     * @return string
     */
    public function index(): string
    {
        // Get page number from the URL
        $page = $_GET["page"] ?? 1;

        // Check if its an int superior to 1
        if (filter_var($page, FILTER_VALIDATE_INT) !== false) {
            $page = max(1, $page);
        } else {
            $page = 1;
        }

        // Get the data from the page
        $pageData = (new ProductManager())->selectPageWithUser($page);

        // If the requested page is superior to the amount of pages,
        // get the last page available.
        if ($page > $pageData["pagesCount"]) {
            $pageData = (new ProductManager())->selectPageWithUser($pageData["pagesCount"]);
        }

        // Render the view
        return $this->twig->render("Product/index.html.twig", [
            "products" => $pageData["products"],
            "currentPage" => $pageData["currentPage"],
            "pagesCount" => $pageData["pagesCount"]
        ]);
    }

    public function show(int $id): string
    {
        $productManager = new productManager();
        $product = $productManager->selectOneById($id);

        $product['photo'] = json_decode($product['photo'], false);

        return $this->twig->render('Product/show.html.twig', ['product' => $product]);
    }
    
    // check if a key of array is empty
    private function checkArray(string $key): void
    {
        if(empty($_POST[$key])) {
            $_POST[$key] = [];
        }
    }
    
    // check if a value of key is empty
    private function checkArrayValue(string $key): string
    {
         $errors = "";
        if (empty($_POST[$key])){
            $errors = "Tu dois séléctionner une icône !";
        }
       
        return $errors;
    }

    public function add(): ?string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST["title"]) || htmlentities(trim($_POST["title"])) === "") {
                $errors["title"] = "Tu as oublié de choisir un titre";
            } elseif (strlen($_POST["title"]) < 2) {
                $errors["title"] = "Ton titre est bien court (2 caractère min)";
            } elseif (strlen($_POST["title"]) > 20) {
                $errors["title"] = "Ton titre est bien long (20 caractère max)";
            }
            if (empty($_POST["description"]) || htmlentities(trim($_POST["description"])) === "") {
                $errors["description"] = "Tu as oublié de décrire ton offre";
            } elseif (strlen($_POST["description"]) < 2) {
                $errors["description"] = "Ta description est bien courte (2 caractère min).";
            } elseif (strlen($_POST["description"]) > 250) {
                $errors["description"] = "Ta description est bien longue (250 caractère max)";
            }
            if (
                empty($_POST["price"]) ||
                (!filter_var($_POST["price"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]))
            ) {
                $errors["price"] = "Ton objet ne peut pas être gratuit (entre un prix supérieur à 0€)";
            }
                
                $this->checkArray("matter");
                $this->checkArray("category");
                $this->checkArray("room");
                $this->checkArray("state");
                $this->checkArray("file");
                $this->checkArray("info");
                $errors["matter"] = $this->checkArrayValue("matter");
                $errors["category"] = $this->checkArrayValue("category");
                $errors["room"] = $this->checkArrayValue("room");
                $errors["state"] = $this->checkArrayValue("state");
                $errors["file"] = $this->checkArrayValue("file");
                $errors["info"] = $this->checkArrayValue("info");
   
            }
            // var_dump($_POST);
            return $this->twig->render("Product/form.html.twig", [
            "errors" => $errors,
            "post" => $_POST
            
            ]);
    }
}        
    

