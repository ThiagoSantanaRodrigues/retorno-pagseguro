<?php

header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");

if (count($_POST) > 0) {

    $email = "vendas@simplework.com.br";
    $token = "E662BD4A9EB34F47AD7D66FEA38A8619";
    //$notificationCode = $_POST['notificationCode'];
    $notificationCode = '2667312FB97AB97A0A8114DD5FA628633347';
    $url = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/"
            . $notificationCode . "?email=" . $email . "&token=" . $token;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $http = curl_getinfo($curl);

    if ($response == 'Unauthorized') {
        print_r($response);
        exit;
    }
    curl_close($curl);
    $response = simplexml_load_string($response);

    if (count($response->error) > 0) {
        print_r($response);
        exit;
    }


    $dataTransacao = $response->date; // Data da criação da transação.
    $codigoTransacao = $response->code; //Código 
    $referencia = $response->reference; //Código de referência da transação.
    $tipoTransacao = $response->type; // Tipo da transação.
    $status = $response->status; //Status da transação.
    $origemCancelamento = $response->cancellationSource; // Origem do cancelamento.
    $dataUltimoEvento = $response->lastEventDate; //Data do último evento.
    $tipoPagamneto = $response->paymentMethod->type; //Tipo do meio de pagamento.
    $meioPagamento = $response->paymentMethod->code; // Código identificador do meio de pagamento
    $valorBrutoTransacao = $response->grossAmount; // Valor bruto da transação.
    $valorDesconto = $response->discountAmount; // Valor do desconto dado.
    $valorTaxasCobradas = $response->feeAmount; // Valor total das taxas cobradas.
    $valorLiquidoTransacao = $response->netAmount; // Valor líquido da transação.
    $dataCredito = $response->escrowEndDate; //Data de crédito.
    $valorExtra = $response->extraAmount; // Data de crédito.
    $numeroParcelas = $response->installmentCount; // Número de parcelas.
    $numeroDeItens = $response->itemCount; // Número de itens da transação.
    $itens = [];
    $arr = json_decode(json_encode($response->items), 1);
    foreach ($arr['item']as $a) {
        $item = [
            'id' => $a['id'],
            'descricao' => $a['description'],
            'quantidade' => $a['quantity'],
            'valor' => $a['amount']
        ];

        array_push($itens, $item);
    }
    $emailComprador = $response->sender->email; //E-mail do comprador.
    $nomeComprador = $response->sender->name; // Nome completo do comprador.
    $codigoAreaComprador = $response->sender->phone->areaCode; //DDD do comprador.
    $telfoneComprador = $response->sender->phone->number; //Número de telefone do comprador.

    if (!empty($response->shipping->type)) {
        $tipoFrete = $response->shipping->type; // Tipo de frete.
    }

    if (!empty($response->shipping->cost)) {
        $custoFrete = $response->shipping->cost; //Custo total do frete.
    }

    if (!empty($response->shipping->address->country)) {
        $enderecoPais = $response->shipping->address->country; //País do endereço de envio.
    }

    if (!empty($response->shipping->address->state)) {
        $enderecoEstado = $response->shipping->address->state; //Estado do endereço de envio.
    }

    if (!empty($response->shipping->address->city)) {
        $enderecoCidade = $response->shipping->address->city; //Cidade do endereço de envio
    }

    if (!empty($response->shipping->address->postalCode)) {
        $enderecoCep = $response->shipping->address->postalCode; //CEP do endereço de envio.
    }

    if (!empty($response->shipping->address->district)) {
        $enderecoBairro = $response->shipping->address->district; //Bairro do endereço de envio.
    }

    if (!empty($response->shipping->address->street)) {
        $enderecoRua = $response->shipping->address->street; //Nome da rua do endereço de envio.
    }

    if (!empty($response->shipping->address->number)) {
        $enderecoNumero = $response->shipping->address->number;  //Número do endereço de envio.
    }

    if (!empty($response->shipping->address->complement)) {
        $enderecoComplemento = $response->shipping->address->complement; //Complemento do endereço de envio.
    }

    // Mostrar Valores
    /*
      echo '<br> Data Transação -> ' . $dataTransacao;
      echo '<br> Codigo transação -> ' . $codigoTransacao;
      echo '<br> Referencia -> ' . $referencia;
      echo '<br> Tipo Transação -> ' . $tipoTransacao;
      echo '<br> Status -> ' . $status;
      echo '<br> Origem Cancelamento -> ' . $origemCancelamento;
      echo '<br> Data Ultimo evento -> ' . $dataUltimoEvento;
      echo '<br> Tipo de Pagamento -> ' . $tipoPagamneto;
      echo '<br> Meio de Pagamento ->' . $meioPagamento;
      echo '<br> Valor Bruto da Transação -> ' . $valorBrutoTransacao;
      echo '<br> Valor de Desconto -> ' . $valorDesconto;
      echo '<br> Valor das Taxas Cobradas - >' . $valorTaxasCobradas;
      echo '<br> Valor Liquido da Transação' . $valorLiquidoTransacao;
      echo '<br> Data de Credito -> ' . $dataCredito;
      echo '<br> Valor Extra -> ' . $valorExtra;
      echo '<br> Numero de Parcelas -> ' . $numeroParcelas;
      echo '<br> Numero de Itens -> ' . $numeroDeItens;
      echo '<br> Itens -> ';
      print_r($itens);
      echo '<br> E-mail do Comprador -> ' . $emailComprador;
      echo '<br> Nome do Comprador -> ' . $nomeComprador;
      echo '<br> Código de Área do comprador -> ' . $codigoAreaComprador;
      echo '<br> Numero Telefone do Comprador -> ' . $telfoneComprador;
      echo '<br> Tipo de Frete ->' . $tipoFrete;
      echo '<br> Custo total do frete -> ' . $custoFrete;
      echo '<br> País do endereço de envio -> ' . $enderecoPais;
      echo '<br> Estado do endereço de envio -> ' . $enderecoEstado;
      echo '<br> Cidade do endereço de envio -> ' . $enderecoCidade;
      echo '<br> CEP do endereço de envio -> ' . $enderecoCep;
      echo '<br> Bairro do endereço de envio -> ' . $enderecoBairro;
      echo '<br> Nome da rua do endereço de envio -> ' . $enderecoRua;
      echo '<br> Número do endereço de envio -> ' . $enderecoNumero;
      echo '<br> Complemento do endereço de envio -> ' . $enderecoComplemento;
     */
}

function formatarData($dataEntrada) {
    $dataSaida = preg_replace("/[^0-9]/", "", trim($dataEntrada));
    $ano = substr($dataSaida, 0, 4);
    $mes = substr($dataSaida, 4, 2);
    $dia = substr($dataSaida, 6, 2);
    $dataSaida = $dia . '/' . $mes . '/' . $ano;
    return $dataSaida;
}

function descricaoStatus($codigo) {
    $status = [
        1 => 'Aguardando pagamento',
        2 => 'Em análise',
        3 => 'Paga',
        4 => 'Disponível',
        5 => 'Em disputa',
        6 => 'Devolvida',
        7 => 'Cancelada'
    ];

    return $status[$codigo];
}

function descricaoDetalhadaStatus($codigo) {
    $status = [
        1 => 'Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.',
        2 => 'Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.',
        3 => 'Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.',
        4 => 'Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.',
        5 => 'Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.',
        6 => 'Devolvida: o valor da transação foi devolvido para o comprador.',
        7 => 'Cancelada: a transação foi cancelada sem ter sido finalizada.'
    ];

    return $status[$codigo];
}

function descricaoTipoPagamento($codigo) {
    $tipoPagamento = [
        1 => 'Cartão de crédito.',
        2 => 'Boleto',
        3 => 'Débito online (TEF)',
        4 => 'Saldo PagSeguro',
        5 => 'Oi Paggo',
        7 => 'Depósito em conta'
    ];

    return $tipoPagamento[$codigo];
}

function descricaoDetalhadaTipoPagamento($codigo) {
    $tipoPagamento = [
        1 => 'Cartão de crédito: O comprador pagou pela transação com um cartão de crédito. Neste caso, o pagamento é processado imediatamente ou no máximo em algumas horas, dependendo da sua classificação de risco.',
        2 => 'Boleto: O comprador optou por pagar com um boleto bancário. Ele terá que imprimir o boleto e pagá-lo na rede bancária. Este tipo de pagamento é confirmado em geral de um a dois dias após o pagamento do boleto. O prazo de vencimento do boleto é de 3 dias.',
        3 => 'Débito online (TEF): O comprador optou por pagar com débito online de algum dos bancos com os quais o PagSeguro está integrado. O PagSeguro irá abrir uma nova janela com o Internet Banking do banco escolhido, onde o comprador irá efetuar o pagamento. Este tipo de pagamento é confirmado normalmente em algumas horas.',
        4 => 'Saldo PagSeguro: O comprador possuía saldo suficiente na sua conta PagSeguro e pagou integralmente pela transação usando seu saldo.',
        5 => 'Oi Paggo *: o comprador paga a transação através de seu celular Oi. A confirmação do pagamento acontece em até duas horas.',
        7 => 'Depósito em conta: o comprador optou por fazer um depósito na conta corrente do PagSeguro. Ele precisará ir até uma agência bancária, fazer o depósito, guardar o comprovante e retornar ao PagSeguro para informar os dados do pagamento. A transação será confirmada somente após a finalização deste processo, que pode levar de 2 a 13 dias úteis.'
    ];

    return $tipoPagamento[$codigo];
}

function descricaoMeioPagamento($codigo) {
    $meioPagamento = [
        101 => 'Cartão de crédito Visa',
        102 => 'Cartão de crédito MasterCard',
        103 => 'Cartão de crédito American Express',
        104 => 'Cartão de crédito Diners',
        105 => 'Cartão de crédito Hipercard',
        106 => 'Cartão de crédito Aura',
        107 => 'Cartão de crédito Elo',
        108 => 'Cartão de crédito PLENOCard',
        109 => 'Cartão de crédito PersonalCard',
        110 => 'Cartão de crédito JCB',
        111 => 'Cartão de crédito Discover',
        112 => 'Cartão de crédito BrasilCard',
        113 => 'Cartão de crédito FORTBRASIL',
        114 => 'Cartão de crédito CARDBAN',
        115 => 'Cartão de crédito VALECARD',
        116 => 'Cartão de crédito Cabal',
        117 => 'Cartão de crédito Mais!',
        118 => 'Cartão de crédito Avista',
        119 => 'Cartão de crédito GRANDCARD',
        120 => 'Cartão de crédito Sorocred',
        201 => 'Boleto Bradesco',
        202 => 'Boleto Santander',
        301 => 'Débito online Bradesco',
        302 => 'Débito online Itaú',
        303 => 'Débito online Unibanco',
        304 => 'Débito online Banco do Brasil',
        305 => 'Débito online Banco Real',
        306 => 'Débito online Banrisul',
        307 => 'Débito online HSBC',
        401 => 'Saldo PagSeguro',
        501 => 'Oi Paggo',
        701 => 'Depósito em conta - Banco do Brasil',
        702 => 'Depósito em conta - HSBC'
    ];

    return $meioPagamento[$codigo];
}
