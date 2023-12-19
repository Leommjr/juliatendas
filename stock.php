<?php

require 'config/config.php';
require 'model/TendaModel.php';


function processMessage($message) {
  
  $users = ['leommjr'];
  $start_options = ['consultar', 'criar', 'alterar', 'deletar'];

  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $sender = $message['from'];
  if (!in_array($sender['username'], $users)){
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '.$sender['first_name'].'. Apenas funcionários da Julia Tendas podem utilizar esse bot!'));
      return;
  }
  if (isset($message['text'])) {
    $text = explode(' ', $message['text']);    
    if (count($text) < 2) {
        sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '.$sender['first_name'].'. Mensagem inválida, tente novamente.'));
        return;
    }
    if (in_array($text[0], $start_options)) {
        $data = handle_message($text);
        sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => $data));
        return;
    }
    else {
        sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '.$sender['first_name'].'. Comando '.$text[0].' inválido!'));
        return;
    }

  }
}

function handle_message($message)
{
    switch($message[0]){
        case 'consultar':
            if ($message[1] === 'tenda')
            {
                $tendas_result = '| Id | Tipo | Descrição | Status | Estado |%0a|----|------|-----------|--------|--------|%0a';
                $tendas = new TendaModel();
                $tendas_disponiveis = $tendas->getAllAvaliableTendas();
                foreach($tendas_disponiveis as $tenda)
                {
                    $tendas_result .= '| '.$tenda["id"].' | '.$tenda["tipo"].' | '.$tenda["descricao"].' | '.$tenda["status"].' | '.$tenda["estado"].' |%0a';
                }
                return $tendas_result;
            }
        default:
            return 'Dados nao encontrados';
    }
}

function sendMessage($method, $parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);

$context  = stream_context_create( $options );
file_get_contents(API_URL.$method, false, $context );
}

$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
  processMessage($update["message"]);
}

?>

