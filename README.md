# Retorno Pagseguro

O arquivo retorno-pagseguro.php tem como finalidade receber uma requisição post do pagseguro com o código da notificação, com esse código fazer a consulta no servidor do pagseguro e preencher a variáveis. 

Algumas variáveis tem como retorno códigos, para praticidade coloquei as seguintes funções que recebem o código e retornam a descrição:
* descricaoStatus
* descricaoDetalhadaStatus
* descricaoTipoPagamento
* descricaoDetalhadaTipoPagamento
* descricaoMeioPagamento
