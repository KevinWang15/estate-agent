<?php
namespace App\Utils;

class APIResponseBuilder
{
  private static $errNo=0, $errMsg="", $responseContent=[], $HTTPStatusCode=200;

  public static function setHTTPStatusCode($code)
  {
    self::$HTTPStatusCode = $code;
  }


  public static function append($key, $value)
  {
    self::$responseContent[$key] = $value;
  }

  public static function err($errNo, $errMsg)
  {
    self::$errNo = $errNo;
    self::$errMsg = $errMsg;
    self::$responseContent = [];
    self::respond();
  }

  public static function respondWithObject($object)
  {
    $object["errNo"] = self::$errNo;
    $object["errMsg"] = self::$errMsg;
    foreach (self::$responseContent as $k => $v) {
      $object[$k] = $v;
    }
    http_response_code(self::$HTTPStatusCode);
    die(json_encode($object));

  }

  public static function respond()
  {

    $response = [];
    $response["errNo"] = self::$errNo;
    $response["errMsg"] = self::$errMsg;
    foreach (self::$responseContent as $k => $v) {
      $response[$k] = $v;
    }
    http_response_code(self::$HTTPStatusCode);
    die(json_encode($response));
  }
}
