<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOficialMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client_name;
    public $order_code;
    public $order_date;
    public $client_document;
    public $order_items;
    public $order_subtotal;
    public $order_igv;
    public $order_total;

    public function __construct($data)
    {
        $this->client_name = $data['client_name'];
        $this->order_code = $data['order_code'];
        $this->order_date = $data['order_date'];
        $this->client_document = $data['client_document'];
        $this->order_items = $data['order_items'];
        $this->order_subtotal = $data['order_subtotal'];
        $this->order_igv = $data['order_igv'];
        $this->order_total = $data['order_total'];
    }

    public function build()
    {
        return $this->markdown('emails.sendOficialMail') // Cambia 'view' por 'markdown'
                    ->subject('Confirmación de Pedido')
                    ->with([
                        'client_name' => $this->client_name,
                        'order_code' => $this->order_code,
                        'order_date' => $this->order_date,
                        'client_document' => $this->client_document,
                        'order_items' => $this->order_items,
                        'order_subtotal' => $this->order_subtotal,
                        'order_igv' => $this->order_igv,
                        'order_total' => $this->order_total,
                    ]);
    }
}
