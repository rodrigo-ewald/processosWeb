<?php

session_start();
require_once("vendor/autoload.php");
use Slim\Slim;
use saes\Page;
use saes\PageLogin;
use saes\Model\User;

$app = new Slim();
$app->config('debug', false);

$app->get('/', function () {
    $page = new PageLogin();
    $page->setTpl(
        'login',
        ['e'=>User::getError()]
    );
});

$app->get('/processos/', function () {
    $page = new PageLogin();
    $page->setTpl(
        'login',
        ['e'=>User::getError()]
    );
});

$app->get('/login', function () {
    $page = new PageLogin();
    $page->setTpl(
        'login',
        ['e'=>User::getError()]
    );
});

$app->get('/recuperarsenha', function () {
    $page = new PageLogin();
    $page->setTpl(
        'forgot',
        ['e'=>User::getError()]
    );
});

$app->get('/recuperarsenha/enviado', function () {
    $page = new PageLogin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl('forgot-sent');
});

$app->get('/recuperarsenha/reset', function () {
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new PageLogin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl('forgot-reset', array(
      "name"=>"Admin",
      "code"=>$_GET["code"]
    ));
});

$app->post('/login', function () {
    try {
        User::login($_POST["email"], $_POST["password"]);
    } catch (Exception $e) {
        User::setError($e->getMessage());
    }
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->post('/processos', function () {
    try {
        User::login($_POST["email"], $_POST["password"]);
    } catch (Exception $e) {
        User::setError($e->getMessage());
    }
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->get('/app/aluno/listagem', function () {
    User::verifyLogin();
    $alunos = User::listAllAlunos();
    $page = new Page();
    $page->setTpl('listagem-alunos', array(
      "alunos"=>$alunos
    ));
});

$app->get("/app/aluno/:idProcesso/deletar", function ($idProcesso) {
    User::verifyLogin();
    $user = new User();
    $user->deleteAluno($idProcesso);
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->get("/app/aluno/movimentos/:idProcesso", function ($idProcesso) {
    User::verifyLogin();
    $cursos = User::listAllCursos();
    $motivos = User::listAllMotivos();
    $aluno = User::loadAluno($idProcesso);
    $movimentos = User::loadMovimentosAluno($idProcesso);
    $page = new Page();
    $page->setTpl('editar-aluno', array(
      "aluno"=>$aluno,
      "movimentos"=>$movimentos,
      "motivos"=>$motivos,
      "cursos"=>$cursos
    ));
});

$app->get('/app/aluno/movimentos/deletar/:idMovimento', function ($idMovimento) {
    User::verifyLogin();
    $idProcesso = User::deleteMovimento($idMovimento);
    foreach($idProcesso as $key=>$value)
    {
      $a= $value;
      foreach($a as $key=>$value)
      {
        $b = $value;
      }
    }
    header('Location: /processos/app/aluno/movimentos/'.$b);
    exit;
});

$app->get('/app/logout', function () {
    User::logout();
    header('Location: /processos/login');
    exit;
});

$app->get('/app/aluno/cadastrar', function () {
    User::verifyLogin();
    $cursos = User::listAllCursos();
    $motivos = User::listAllMotivos();
    $page = new Page();
    $page->setTpl('cadastrar-aluno', array(
      "cursos"=>$cursos,
      "motivos"=>$motivos
    ));
});

$app->get('/app/curso/cadastrar', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('cadastrar-curso');
});

$app->get('/app/motivo/cadastrar', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('cadastrar-motivo');
});

$app->post('/app/aluno/cadastrar', function () {
    User::verifyLogin();
    $aluno = new User();
    $_POST["priEnvio"] = (User::checkDate($_POST["priEnvio"]) !== "1") ? null : $_POST["priEnvio"];
    $_POST["anoGrau"] = (User::checkDate($_POST["anoGrau"]) !== "1") ? null : $_POST["anoGrau"];
    $_POST["numCI"] = ($_POST["numCI"] == "") ? null : $_POST["numCI"];
    $_POST["numProcesso"] = ($_POST["numProcesso"] == "") ? null : $_POST["numProcesso"];
    $_POST["concluido"] = (isset($_POST["concluido"])) ? 1 : 0;
    $aluno->setData($_POST);
    $aluno->saveAluno();
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->post('/app/curso/cadastrar', function () {
    User::verifyLogin();
    $curso = new User();
    $curso->setData($_POST);
    $curso->saveCurso();
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->post('/app/motivo/cadastrar', function () {
    User::verifyLogin();
    $motivo = new User();
    $motivo->setData($_POST);
    $motivo->saveMotivo();
    header('Location: /processos/app/aluno/listagem');
    exit;
});

$app->post("/recuperarsenha", function () {
    $user = User::getForgot($_POST["email"]);
    header("Location: /processos/recuperarsenha/enviado");
    exit;
});

$app->post('/recuperarsenha/reset', function () {
    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["idrecuperacao"]);
    $user = new User();
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);
    $user->setPasswordForgot($password, $forgot["id"]);
    $page = new PageLogin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl('forgot-reset-success');
});

$app->post('/app/aluno/processo/editar/:idProcesso', function ($idProcesso) {
  User::verifyLogin();
  $editarProcesso = new User();
  $_POST["priEnvio"] = (User::checkDate($_POST["priEnvio"]) !== "1") ? null : $_POST["priEnvio"];
  $_POST["anoGrau"] = (User::checkDate($_POST["anoGrau"]) !== "1") ? null : $_POST["anoGrau"];
  $_POST["numCI"] = ($_POST["numCI"] == "") ? null : $_POST["numCI"];
  $_POST["numProcesso"] = ($_POST["numProcesso"] == "") ? null : $_POST["numProcesso"];
  $_POST["concluido"] = (isset($_POST["concluido"])) ? 1 : 0;
  $editarProcesso->get((int)$idProcesso);
  $editarProcesso->setData($_POST);
  $editarProcesso->editAluno();
  header("Location: /processos/app/aluno/movimentos/$idProcesso");
  exit;
});

$app->post('/app/aluno/movimentos/:idProcesso', function ($idProcesso) {
    User::verifyLogin();
    $movimento = new User();
    $_POST["dataMovimento"] = (User::checkDate($_POST["dataMovimento"]) !== "1") ? null : $_POST["dataMovimento"];
    $movimento->get((int)$idProcesso);
    $movimento->setData($_POST);
    $movimento->saveMovimento();
    header('refresh: 1;');
    exit;
});

$app->run();
