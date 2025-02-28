<x-mail::message>
# ¡Hola {{ $client_name }}!

Te confirmamos que tu pedido **#{{ $order_code }}** ha sido recibido
y se encuentra en proceso de aprobación.
Quédate atento que apenas tu pedido sea aprobado, te enviaremos un
correo electrónico y un mensaje por WhatsApp desde el <strong>991 375 813</strong>
con los medios de pago.
Ten en cuenta que el envío y/o entrega se realizara una vez el producto
este facturado

## ¿Cuál es el resumen de mi pedido?

<x-mail::panel>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="text-align: left; padding: 8px;">
                <strong>N° Pedido:</strong><br>
                {{ $order_code }}<br><br>
                <strong>Titular:</strong><br>
                {{ $client_name }}
            </td>
            <td style="text-align: left; padding: 8px;">
                <strong>Fecha de pedido:</strong><br>
                {{ $order_date }}<br><br>
                <strong>Documento de identidad:</strong><br>
                {{ $client_document }}
            </td>
        </tr>
    </table>
</x-mail::panel>

<x-mail::table>


| Producto | Cantidad | Precio (S/.) | Total (S/.) |
|:-------- |:--------:|:------------:|:-----------:|
@foreach($order_items as $item)
| {{ $item['product_name'] }} | {{ $item['product_quantity'] }} | {{ $item['product_price'] }} | {{ $item['product_total'] }} |
@endforeach

</x-mail::table>

**Sub total: S/** {{ $order_subtotal }}<br>
**IGV: S/** {{ $order_igv }}<br>
<div style="color: #fff; background-color: #f6711f; padding: 10px; border-radius: 10px; margin-bottom: 1rem">
    <strong>Total: S/ </strong><strong>{{ $order_total }}</strong>
</div>

<table style="width: 100%; background-color: #f6711f; color: white; padding: 20px; border-radius: 10px;">
    <tr>
        <td style="text-align: center;">
          <strong style="font-size: .7rem; background-color: white; color: #f6711f; padding: .2rem .5rem; border-radius: .5rem;text-align: center;">
              Método de Pago
          </strong>
          <br><br>
          <img src="https://grupoalmerco.com.pe/Interbank.png" alt="Interbank" style="width: 150px;">
        </td>
        <td style="padding-left: 20px;">
          <div style="font-size: .8rem; background-color: white; color: #f6711f; padding: 10px; border-radius: 10px; text-align: center">
              <span>Cuenta Corriente</span><br>
              <strong>Grupo Almerco Eirl</strong><br>
              <span>N° de Cuenta en Soles</span><br>
              <strong>561-3006236467</strong><br>
              <span>Código CCI Interbancario</span><br>
              <strong>003-561-003006236467-01</strong>
          </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 20px; color: white;">
            Aviso: Toda la comercialización es mediante la empresa <strong>Grupo Almerco Eirl</strong>. Por tal motivo la facturación, depósitos, transferencias y otros son exclusivamente a nombre de la misma.
        </td>
    </tr>
</table>

<x-mail::subcopy>
    <table style="width: 100%; background-color: #333333; color: white; padding: 20px; border-radius: 10px;">
        <tr>
            <td style="text-align: left;">
                <div style="display: flex; align-items: center; height: 100%;">
                    <!-- Ícono y texto "Estamos ubicados" -->
                    <div style="flex-shrink: 0; font-size: 1.5rem; margin-right: 0.5rem;">
                        &#x1f4cc; <!-- Ícono de ubicación -->
                    </div>
                    <div>
                        <strong style="color: #ea565a; font-size: .8rem; display: block; line-height: 1rem;">
                            Estamos <br> ubicados
                        </strong>
                        <!-- Dirección -->
                        <span style="font-size: .9rem; font-weight: 600;">Jr. Huallayco 1135</span><br>
                        <span style="font-size: .6rem;">a media cuadra del colegio Leoncio Prado</span>
                    </div>
                </div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <a href="https://grupoalmerco.com.pe" style="background-color: #FFFFFF; color: #333333; padding: .3rem .5rem; text-decoration: none; border-radius: 5px; font-size: .7rem; text-align: center; display: inline-block;">
                    www.grupoalmerco.com.pe
                </a>
                <br><br>
                <div>
                    <a href="https://www.facebook.com/GrupoalmercoPeru/" style="text-decoration: none; margin-right: .3rem;">
                      <img src="https://grupoalmerco.com.pe/facebook.png" alt="Facebook" style="width: 20px; height: 20px;">
                    </a>
                    <a href="https://api.whatsapp.com/send/?phone=51991375813&text=%C2%A1Hola%21+Quiero+recibir+mas+informaci%C3%B3n+y+promociones.&type=phone_number&app_absent=0" style="text-decoration: none; margin: 0 .3rem;">
                      <img src="https://grupoalmerco.com.pe/instagram.webp" alt="Instagram" style="width: 20px; height: 20px;">
                    </a>
                    <a href="https://www.tiktok.com/@grupo_almerco" style="text-decoration: none; margin-left: .3rem;">
                      <img src="https://grupoalmerco.com.pe/tiktok.webp" alt="TikTok" style="width: 20px; height: 20px;">
                    </a>
                </div>
            </td>
        </tr>
    </table>
</x-mail::subcopy>

</x-mail::message>
