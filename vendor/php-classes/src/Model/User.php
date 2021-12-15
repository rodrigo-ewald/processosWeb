<?php

namespace saes\Model;

use saes\DB\Sql;
use saes\Model;
use saes\Mailer;

class User extends Model
{

    public const SESSION = "User";
    public const ERROR = "UserError";
    public const KEY = '8R:Hl"2U.!02l,!#U[nj|,-u.OX#=}^-3H';//aa
    public const KEY_IV = '}bh~o<>}N+(/skCeYH:*k)Y5n\lRGEg6L#';//AA
    public const ALGORITHM_NAME = "AES-128-CBC";
    public const ALGORITHM_NONCE_SIZE = "12";
    protected $fields = ["nome", "concluido", "numCI", "numProcesso", "anoGrau", "priEnvio", "idCurso", "idMotivo"];

    public static function login($email, $password)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM usuarios WHERE email = :EMAIL", array(
      ":EMAIL"=>$email
    ));
        if (count($results) === 0) {
            throw new \Exception("E-mail inexistente ou senha inválida.");
        }

        $data = $results[0];

        if (password_verify($password, $data["password"]) === true) {
            $user = new User();
            $user->setData($data);
            $_SESSION[User::SESSION] = $user->getValues();
            return $user;
        } else {
            throw new \Exception("E-mail inexistente ou senha inválida.");
        }
    }

    public static function verifyLogin()
    {
        if (!isset($_SESSION[User::SESSION ]) || !$_SESSION[User::SESSION] || !(int)$_SESSION[User::SESSION]["id"] > 0) {
            header("Location: /processos/login");
            exit;
        }
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }


    public function get($idProcesso)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM processos WHERE idProcesso = :idProcesso", array(
          ":idProcesso"=>$idProcesso
        ));
        $this->setData($results[0]);
    }

    public static function loadAluno($idProcesso)
    {
        $sql = new Sql();
        return $results = $sql->select("SELECT * FROM processos a INNER JOIN motivos b ON a.idMotivo = b.id INNER JOIN cursos c USING(idCurso) WHERE a.idProcesso= $idProcesso;");
    }

    public static function loadMovimentosAluno($idProcesso)
    {
        $sql = new Sql();
        return $results = $sql->select("SELECT * FROM movimentos where idProcesso = $idProcesso ORDER BY dataMovimento;");
    }

    public static function listAllAlunos()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM processos ORDER BY idProcesso;");
    }

    public static function listAllCursos()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM cursos;");
    }

    public static function listAllMotivos()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM motivos;");
    }

    public function saveAluno()
    {
        $sql = new Sql();
        $results = $sql->select("INSERT INTO processos(nome, concluido, numCI, numProcesso, anoGrau, priEnvio, idCurso, idMotivo) VALUES (:nome, :concluido, :numCI, :numProcesso, :anoGrau, :priEnvio, :idCurso, :idMotivo)",array(
          ":nome"=>$this->getnome(),
          ":concluido"=>$this->getconcluido(),
          ":numCI"=>$this->getnumCI(),
          ":numProcesso"=>$this->getnumProcesso(),
          ":anoGrau"=>$this->getanoGrau(),
          ":priEnvio"=>$this->getpriEnvio(),
          ":idCurso"=>$this->getidCurso(),
          ":idMotivo"=>$this->getidMotivo()
      ));
        $this->setData($results);
    }

    public static function removeMask($date)
    {
        $date = preg_replace('/[_]/', '', $date);
        $date = preg_replace('/[-]/', '', $date);
        return $date;
    }

    public static function checkDate($date)
    {
        $date = User::removeMask($date);
        if ($date == "" || \strlen($date) !== 8 || $date =="00000000") {
            return null;
        } else {
            return "1";
        }
    }

    public function saveCurso()
    {
        $sql = new Sql();
        $results = $sql->select("INSERT INTO cursos(nomeCurso, sigla) VALUES(:nomeCurso, :sigla)", array(
          ":nomeCurso"=>$this->getnomeCurso(),
          ":sigla"=>$this->getsigla()
        ));
    }

    public function saveMotivo()
    {
        $sql = new Sql();
        $results = $sql->select("INSERT INTO motivos(motivo) VALUES(:motivo)", array(
          ":motivo"=>$this->getmotivo()
        ));
    }

    public static function setError($msg)
    {
        $_SESSION[User::ERROR] = $msg;
    }

    public static function getError()
    {
        $msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';
        User::clearError();
        return $msg;
    }

    public static function clearError()
    {
        $_SESSION[User::ERROR] = null;
    }

    public static function getForgot($email)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM usuarios WHERE email = :email;", array(
          ":email"=>$email
        ));
        if (count($results) === 0) {
            throw new \Exception("Caso este e-mail seja válido, foi enviado um link de recuperação!");
        } else {
            $data= $results[0];
            $results2 = $sql->select("CALL recuperacaosenhas_criar(:id, :userip)", array(
              ":id"=>$results[0]["id"],
              ":userip"=>$_SERVER["REMOTE_ADDR"]
            ));
            if (count($results2) === 0) {
                throw new \Exception("Caso este e-mail seja válido, foi enviado um link de recuperação!");
            } else {
                $code = openssl_encrypt($results2[0]["idrecuperacao"], User::ALGORITHM_NAME, pack("a16", User::KEY), 0, pack("a16", User::KEY_IV));
                $code = base64_encode($code);
                $link = "address/processos/recuperarsenha/reset?code=$code";
                $mailer = new Mailer($data["email"], "Admin", "Redefinir senha - SAES Movimento", "forgot", array(
                  "name"=>"Admin",
                  "link"=>$link
                ));
                return $data;
            }
        }
    }

    public static function validForgotDecrypt($code)
    {
        $code = base64_decode($code);
        $idrecuperacao = openssl_decrypt($code, User::ALGORITHM_NAME, pack("a16", User::KEY), 0, pack("a16", User::KEY_IV));
        $sql = new Sql();
        $results = $sql->select(
            "SELECT * FROM recuperacaoSenhas INNER JOIN usuarios USING(id)
             WHERE
				     recuperacaoSenhas.idrecuperacao = :idrecuperacao
				     AND
				     recuperacaoSenhas.datarecuperacao IS NULL
				     AND
				     DATE_ADD(recuperacaoSenhas.dataregistro, INTERVAL 1 HOUR) >= NOW();",
             array(
                "idrecuperacao"=> $idrecuperacao
             ));
        if (count($results) === 0) {
            throw new \Exception("Caso este e-mail seja válido, foi enviado um link de recuperação!03");
        } else {
            return $results[0];
        }
    }

    public static function setForgotUsed($idrecuperacao)
    {
        $sql = new Sql();
        $sql->select("UPDATE recuperacaoSenhas SET datarecuperacao = NOW() WHERE idrecuperacao = :idrecuperacao", array(
          ":idrecuperacao"=>$idrecuperacao
        ));
    }

    public function setPasswordForgot($password, $id)
    {
        $sql = new Sql();
        $sql->select("UPDATE usuarios SET password = :password WHERE id = :id", array(
          ":password"=>$password,
          ":id"=>$id
        ));
    }

    public function editAluno()
    {
      $sql = new Sql();
      $results = $sql->select("UPDATE processos SET
        nome = :nome,
        concluido = :concluido,
        numCI = :numCI,
        numProcesso = :numProcesso,
        anoGrau = :anoGrau,
        priEnvio = :priEnvio,
        idCurso = :idCurso,
        idMotivo = :idMotivo
        WHERE idProcesso = :idProcesso;", array(
          ":nome"=>$this->getnome(),
          ":concluido"=>$this->getconcluido(),
          ":numCI"=>$this->getnumCI(),
          ":numProcesso"=>$this->getnumProcesso(),
          ":anoGrau"=>$this->getanoGrau(),
          ":priEnvio"=>$this->getpriEnvio(),
          ":idCurso"=>$this->getidCurso(),
          ":idMotivo"=>$this->getidMotivo(),
          ":idProcesso"=>$this->getidProcesso()
      ));
      $this->setData($results);
    }

    public function saveMovimento()
    {
        $sql = new Sql();
        $results = $sql->select("INSERT INTO movimentos(movimento, destino, dataMovimento, idProcesso) values (:movimento, :destino, :dataMovimento, :idProcesso)", array(
          ":movimento"=>$this->getmovimento(),
          ":destino"=>$this->getdestino(),
          ":dataMovimento"=>$this->getdataMovimento(),
          ":idProcesso"=>$this->getidProcesso()
        ));
        $this->setData($results);
    }

    public function deleteAluno($idProcesso)
    {
        $sql = new Sql();
        $sql->select("DELETE FROM processos WHERE idProcesso = :idProcesso;", array(
          ":idProcesso"=>$idProcesso
        ));
    }

    public static function deleteMovimento($idMovimento)
    {
        $sql = new Sql();
        $result = $sql->select("SELECT (idProcesso) FROM movimentos WHERE idMovimento = $idMovimento");
        $sql->select("DELETE FROM movimentos WHERE idMovimento = $idMovimento;");
        return $result;
    }

}
