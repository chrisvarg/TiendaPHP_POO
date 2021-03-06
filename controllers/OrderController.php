<?php
require_once 'models/Order.php';
require_once 'models/Product.php';

class OrderController
{
    public function make()
    {
        require_once 'views/order/make.php';
    }

    public function add() 
    {   
        Utils::isLogin();
        if(isset($_POST) && !empty($_POST)){
            // id user session
            $user = $_SESSION['identity'];
            $user_id = $user->id;

            // form data
            $province = isset($_POST['province']) ? $_POST['province'] : false;
            $location = isset($_POST['location']) ? $_POST['location'] : false;
            $address = isset($_POST['address']) ? $_POST['address'] : false;

            //order cost
            $stats = Utils::statsCart();
            $cost = $stats['total'];

            if($province && $location && $address) {

                $order = new Order;
                $order->setId_user($user_id);
                $order->setProvince($province);
                $order->setLocation($location);
                $order->setAddress($address);
                $order->setCost($cost);
                
                // SAVE ORDER
                $save = $order->save();
                
                // SAVE ORDER LINE
                $save_line = $order->save_lines();

                if($save && $save_line) {
                    $_SESSION['order'] = "complete";
                    $this->updateStockProduct();

                } else {
                    $_SESSION['order'] = "failed1";
                }
                
            } else {
                $_SESSION['order'] = "failed2";
            }

            header("Location:".BASE_URL. "order/confirm");

        }else {
            header("Location:".BASE_URL);
        }
    }

    public function confirm()
    {
        // LOGUEADO/ ID_USER
        Utils::isLogin();
        $user = $_SESSION['identity'];
        $user_id = $user->id; 

        $order = new Order();
        $order->setId_user($user_id);

        // ORDER LAST
        $ord = $order->GetOneByUser();

        // ORDER PRODUCTS
        $order_product = new Order();
        $products = $order_product->getAllProductsByUser($ord->id);
        
        require_once 'views/order/confirm.php';
    }

    public function myOrders()
    {
        Utils::isLogin();
        $user =$_SESSION['identity'];
        $user_id = $user->id;

        $order = new Order();
        $order->setId_user($user_id);
        $orders = $order->getAllByUser();

        // User Orders
        require_once 'views/order/myOrders.php';
    }


    public function detail()
    {
        Utils::isLogin();
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            
            $order_id = $_GET['id'];

            // ORDER
            $order = new Order();
            $order->setId($order_id);
            $ord = $order->getOne();

            // ORDER PRODUCT
            $order_products = new Order();
            $products = $order_products->getAllProductsByUser($order_id);

            // updateUnitsProduct($or)

            if(isset($_SESSION['admin'])){

                $order_user = new Order();
                $order_user->setId_user($ord->id_usuario);
                $order_user->setId($order_id);

                $user = $order_user->getDataUser();
            }
        }
        
        require_once 'views/order/detail.php';
    }

    
    public function management()
    {
        Utils::isAdmin();
        $management = true;
        
        $order = new Order();
        $orders = $order->getAll();
        require_once 'views/order/myOrders.php';
    }

    public function state() 
    {
        Utils::isAdmin();
        // Utils::debugDate($_POST);

        if(isset($_POST['order_id']) && isset($_POST['state'])) {
            
            $id_order = $_POST['order_id'];
            $state = $_POST['state'];

            $order = new Order();
            $order->setId($id_order);
            $order->setState($state);

            $save = $order->update();

            header('Location:' . BASE_URL . 'order/detail&id='.$id_order);
        } else {
            header('Location:' . BASE_URL);
        }
    }

    public function updateStockProduct() 
    {
        // LLAMAR LA ULTIMO PEDIDO 
        $user_identity = $_SESSION['identity'];
        $user_id = $user_identity->id;

        $order = new Order();
        $order->setId_user($user_id);
        $lastOrder = $order->getLastOrderUser();

        // TRAER LOS PRODUCTOS DEL PEDIDO
        $order_product = new Order();
        $products = $order_product->getAllProductsByUser($lastOrder->id);

        
        while($product = $products->fetch_object()){
            $stock = ((int)$product->stock) - ((int)$product->units);
            // ACTUALIZAR LOS DATOS

            $product_update = new Product();
            $product_update->setId($product->id);
            $product_update->setStock($stock);

            $product_update->updateStockProduct();
 

        }
    }

}