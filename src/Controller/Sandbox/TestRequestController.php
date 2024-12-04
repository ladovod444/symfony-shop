<?php

namespace App\Controller\Sandbox;

use App\Repository\UserRepository;
use App\Service\ProductImport;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;


#[WithMonologChannel('import')]
class TestRequestController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly ProductImport $import,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/test/request', name: 'app_test_request')]
    public function index(Request $request): Response
    {
        /**
         * Symfony\Component\HttpFoundation\InputBag {#17 ▼
         * #parameters: array:1 [▼
         * "PHPSESSID" => "aff379c44d098c0f1301718959fa7cf7"
         * ]
         * }
         */

        //dd($request->cookies);

        /**
         * TestRequestController.php on line 25:
         * Symfony\Component\HttpFoundation\InputBag {#10 ▼
         * #parameters: []
         * }
         */
//        dd($request->request);

        /**
         * array:6 [▼
         * "_stopwatch_token" => "328b73"
         * "_route" => "app_test_request"
         * "_controller" => "App\Controller\TestRequestController::index"
         * "_route_params" => []
         * "_firewall_context" => "security.firewall.map.context.main"
         * "_access_control_attributes" => null
         * ]
         */
//        dd($request->attributes->all());

        /**
         * Содержит разнообразную инфу о серверных переменных
         * а также переменных окружения, типа
         * "SERVER_NAME" => "symfony-shop.ddev.site"
         * "SERVER_PORT" => "80"
         * "SERVER_ADDR" => "172.19.0.7"
         * "REMOTE_PORT" => "48170"
         * "REMOTE_ADDR" => "172.19.0.8"
         * "SERVER_SOFTWARE" => "nginx/1.26.2"
         * "APP_SECRET" => "e4dca69834b3b3c96cdaf62a19164360"
         * "DB_USER" => "db"
         * "DB_PASS" => "db"
         * "DB_HOST" => "db"
         * "DB_APP" => "db"
         * "RABBITMQ_URL" => "amqp://rabbitmq:rabbitmq@rabbitmq:5672/%2f/async"
         * "RABBITMQ_URL_RETAILCRM" => "amqp://rabbitmq:rabbitmq@rabbitmq:5672/%2f/retailcrm"
         */
        //dd($request->server);


        /**
         * Заголовки отправляемые браузером,
         * Например
         * "cookie" => array:1 [▼
         * 0 => "PHPSESSID=aff379c44d098c0f1301718959fa7cf7"
         * ]
         * "cache-control" => array:1 [▶]
         * "accept-language" => array:1 [▶]
         * "accept-encoding" => array:1 [▶]
         * "accept" => array:1 [▶]
         * "user-agent" => array:1 [▶]
         * "host" => array:1 [▼
         *    0 => "symfony-shop.ddev.site"
         * ]
         */
//        dd($request->headers);


        /**
         * Возвратит просто путь - "/test/request"
         */
        // dd($request->getPathInfo());

//        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
//        if ($acceptHeader->has('text/html')) {
//            $item = $acceptHeader->get('text/html');
//            $charset = $item->getAttribute('charset', 'utf-8');
//            $quality = $item->getQuality();
//
//            echo $item . ' ' . $charset . ' ' . $quality; // text/html utf-8 1
//        }


        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        //This information can also be manipulated after the Response object creation:


//        $response->setContent('Hello World');
//
//        // the headers public attribute is a ResponseHeaderBag
//        $response->headers->set('Content-Type', 'text/plain');
//
//        $response->setStatusCode(Response::HTTP_NOT_FOUND);
//        $response->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);
//
//        $response->send();

        //

        $count = $request->cookies->get('count') ?? 0;
//        var_dump($count);

        $count++;
        $cookie = Cookie::create('count')
            ->withValue($count)
            ->withExpires(strtotime('Fri, 20-May-2025 15:25:52 GMT'))
//            ->withDomain('symfony-shop.ddev.site/')
            ->withSecure(true);

        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        //var_dump($request->cookies); //die();

        $cookie = $request->cookies->get('count');

//        var_dump($cookie);


        return $this->render('test_request/index.html.twig', [
            'controller_name' => 'TestRequestController',
            'count' => $count,
        ]);
    }

    #[Route('/test/stream', name: 'app_test_request_stream')]
    public function indexStream(Request $request): StreamedJsonResponse
    {
//        $response = new StreamedResponse();
//        $response->setCallback(function (): void {
//            var_dump('Hello World');
//            flush();
//            sleep(2);
//            var_dump('Hello World2');
//            flush();
//            sleep(2);
//            var_dump('Hello World3');
//            flush();
//        });
//        $response->send();

        $response = new StreamedJsonResponse(
        // JSON structure with generators in which will be streamed as a list
            [
                '_embedded' => [
                    'articles' => $this->loadArticles(),
                ],
            ],
        );

        return $response;
    }

    function loadArticles(): \Generator
    {
        yield ['title' => 'Article 1'];
        yield ['title' => 'Article 2'];
        yield ['title' => 'Article 3'];
    }

    #[Route('/test/json-file', name: 'app_test_request_json_file')]
    public function getJson(Request $request): Response
    {

//        $user = $this->userRepository->find(
//            $this->parameterBag->get('app:import_product_author')
//        );
//        $items = Items::fromFile(
//            './uploads/products.json',
//            ['decoder' => new ErrorWrappingDecoder(new ExtJsonDecoder())]
//        );
//
//        $count = 0;
//        foreach ($items as $key => $item) {
//            if ($key instanceof DecodingError || $item instanceof DecodingError) {
//                // handle error of this malformed json item
//                continue;
//            }
//            //var_dump($key, $item);
//
//            $product = new Product();
//            $product->setTitle($item->title);
//            $product->setCurrentPrice($item->price);
//            $product->setRegularPrice($item->price);
//            $product->setDescription("test product ".$item->title);
//            $category = $this->import->randomCategory();
//            $product->setCategory($category);
//            $product->setUser($user);
//
//            $this->entityManager->persist($product);
//
//            $count ++;
//
//            if ($count == 20) {
//                $this->entityManager->flush();
//                $count = 0;
//            }
//
//        }

        die();
    }


    #[Route('/test/test-controller', name: 'app_test_controller')]
    public function testController(Request $request, LoggerInterface $logger): Response {
       $url = $this->generateUrl('app_test_controller');

       $logger->info("Info from $url");

//        return $this->redirectToRoute('app_test_request_stream', $request->query->all());

       return new Response($url);
    }

    #[Route('/test/map-query', name: 'app_test_request_map_quer')]
    public function testMapQuery(
        #[MapQueryParameter] string $firstName,
        #[MapQueryParameter] string $lastName,
        #[MapQueryParameter] int $age = 25,
    ): Response
    {
        // ...
        return new Response($firstName . ' ' . $lastName . ' ' . $age);
    }

    #[Route('/test/test-session', name: 'app_test_session')]
    public function testSession(Request $request, LoggerInterface $logger): Response {
        $session = $request->getSession();

        $count = $session->get('count') ?? 0;
        $count++;
        $session->set('count', $count);

        return $this->json(['visits_count' => $count], Response::HTTP_OK, ['Content-Type' => 'application/json']);

        //return new Response($count);

        return new Response($session->getName());
    }

    ///flash/message
     #[Route('/test/test-flash', name: 'app_test_flash')]
     public function testFlash(Request $request, LoggerInterface $logger): Response {
        $this->addFlash('error', "Testing flash!!!");

        return $this->redirectToRoute('app_flash_message');
     }


}
