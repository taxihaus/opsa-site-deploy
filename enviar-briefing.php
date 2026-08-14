<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
    exit;
}

function campo($nome) {
    return isset($_POST[$nome]) ? trim($_POST[$nome]) : '';
}

// Honeypot — campo invisível que só bots preenchem. Se vier preenchido,
// finge sucesso (não denuncia a armadilha) e não envia nada.
if (campo('website') !== '') {
    echo json_encode(['success' => true]);
    exit;
}

$nome     = campo('nome');
$email    = campo('email');
$empresa  = campo('empresa');
$demanda  = campo('demanda');
$prazo    = campo('prazo');
$cenario  = campo('cenario');

if ($nome === '' && $email === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'campos_obrigatorios']);
    exit;
}

// Remove quebras de linha dos campos usados em cabeçalhos, prevenindo header injection.
function limpar($valor) {
    return str_replace(["\r", "\n"], ' ', $valor);
}

$nome    = limpar($nome);
$email   = limpar($email);
$empresa = limpar($empresa);
$demanda = limpar($demanda);
$prazo   = limpar($prazo);

$destino = 'contato@odilonpereira.com.br';
$assunto = 'Novo briefing juridico pelo site';

$corpo  = "Nome: {$nome}\n";
$corpo .= "E-mail: {$email}\n";
$corpo .= "Empresa: {$empresa}\n";
$corpo .= "Natureza da demanda: {$demanda}\n";
$corpo .= "Prazo envolvido: {$prazo}\n";
$corpo .= "Cenario:\n{$cenario}\n";

$remetente = 'site@odilonpereira.com.br';
$headers  = "From: Site OPSA <{$remetente}>\r\n";
if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $headers .= "Reply-To: {$email}\r\n";
}
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$assuntoCodificado = '=?UTF-8?B?' . base64_encode($assunto) . '?=';
$enviado = mail($destino, $assuntoCodificado, $corpo, $headers);

if ($enviado) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'falha_envio']);
}
