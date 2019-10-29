<?php 
namespace NeroOssidiana {

    use \Slim\Views\PhpRenderer as ViewRenderer;
    use NeroOssidiana\CheckoutRepository as CheckoutRepo;

    use PayPal\Api\Amount;
    use PayPal\Api\Details;
    use PayPal\Api\Item;
    use PayPal\Api\ItemList;
    use PayPal\Api\Payer;
    use PayPal\Api\Payment;
    use PayPal\Api\RedirectUrls;
    use PayPal\Api\Transaction;
    use PayPal\Api\ExecutePayment;
    use PayPal\Api\PaymentExecution;

    class PaymentController
    {
        protected $view;
        private $repository;
        private $paypalContext;

        public function __construct(ViewRenderer $view, CheckoutRepo $repository, $paypalConf)
        {
            $this->view = $view;
            $this->repository = $repository;
            $this->paypalContext = $paypalConf;
        }

        public function __invoke($request, $response, $args)
        {
            Console::log(count($_SESSION["Cart"]) . "" . count($_SESSION["Customer"]));
            if (count($_SESSION["Cart"]) == 0 || count($_SESSION["Customer"]) == 0) {
            }

            $response = $this->view->render($response, 'payment.phtml', [
                "total" => Cart::getTotal(),
                "shippingCosts" => CheckoutModel::getShippingCosts()
            ]);
            return $response;
        }

        public function PaypalPay($request, $response, $args)
        {
            $formValues = $request->getParsedBody();

            # è importante aggiungere questa funzione ad ogni metodo di pagamento.
            Order::add($formValues["order-total"]);

            # Create new payer and method
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");

            # Set redirect URLs
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl("http://" . $_SERVER["SERVER_NAME"] . "/Pagamento/paypal/process")
                ->setCancelUrl("http://" . $_SERVER["SERVER_NAME"] . "/Pagamento/paypal/cancel");

            # Set payment amount
            $amount = new Amount();
            $amount->setCurrency("EUR")
                ->setTotal($formValues["order-total"]);

            # Set transaction object
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setDescription("Payment description");

            # Create the full payment object
            $payment = new Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));

            # Create payment with valid API context
            try {
                $payment->create($this->paypalContext);

                # Get PayPal redirect URL and redirect the customer
                $approvalUrl = $payment->getApprovalLink();

                # Redirect the customer to $approvalUrl
                header("Location: $approvalUrl");
                exit;
            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            } catch (Exception $ex) {
                die($ex);
            }
        }

        public function PaypalProcess($request, $response, $args)
        {
            # Get payment object by passing paymentId
            $paymentId = $_GET['paymentId'];
            $payment = Payment::get($paymentId, $this->paypalContext);
            $payerId = $_GET['PayerID'];

            # Execute payment with payer ID
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            try {
                # Execute payment
                // $result = $payment->execute($execution, $this->paypalContext);

                $this->repository->placeOrder();

                header("Location: /Grazie");
                exit;
            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            } catch (Exception $ex) {
                die($ex);
            }
        }

        public function PaypalCancel()
        {
            Order::clear();
            
            header("Location: /");
            exit;
        }

        public function Confirm($request, $response, $args)
        {
            Cart::clear();
            Order::clear();

            $response = $this->view->render($response, 'thanks.phtml', []);
            return $response;
        }
    }
}
