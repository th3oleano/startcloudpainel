<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProxmoxController extends Controller
{
    public function maquinasOnline()
    {
        $proxmoxUrl = 'https://192.168.15.7:8006/api2/json';
        $username = 'root@pam'; // Altere para seu usuário
        $password = 'Leozinho19@'; // Altere para sua senha

        // 1. Autentica e pega o ticket
        $client = new Client(['verify' => false]);
        $authResponse = $client->post("$proxmoxUrl/access/ticket", [
            'form_params' => [
                'username' => $username,
                'password' => $password,
            ]
        ]);
        $authData = json_decode($authResponse->getBody(), true)['data'];
        $ticket = $authData['ticket'];
        $csrfToken = $authData['CSRFPreventionToken'];

        // 2. Busca as VMs
        $node = 'pve'; // Altere para o nome do seu node se necessário
        $response = $client->get("$proxmoxUrl/nodes/$node/qemu", [
            'headers' => [
                'Cookie' => "PVEAuthCookie=$ticket"
            ]
        ]);
        $vms = json_decode($response->getBody(), true)['data'];

        // 3. Filtra apenas as online
        $online = array_filter($vms, function($vm) {
            return $vm['status'] === 'running';
        });

        return response()->json(array_values($online));
    }
}
