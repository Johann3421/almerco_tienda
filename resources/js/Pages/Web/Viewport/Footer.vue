<script setup>
import References from "@/Pages/Web/Viewport/References.vue";
import Logo from "@assets/img/logo.png";
import Auriculares from "@assets/img/extra/auriculares.png";
import facebook from "@assets/img/extra/facebook.png";
import whatsapp from "@assets/img/extra/whatsapp.png";
import tiktok from "@assets/img/extra/tiktok.png";
import bcp from "@assets/img/extra/bcp.png";
import bbva from "@assets/img/extra/bbva.png";
import visa from "@assets/img/extra/visa.png";
import interbank from "@assets/img/extra/interbank.png";
</script>

<template>
  <References />
  <footer class="bg-gradient-to-b from-navegation to-encabezado">
    <div class="mx-auto sm:px-6 px-8">
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 justify-center items-center py-12">
        <div class="flex flex-col md:flex-row lg:flex-col justify-center items-center gap-4">
          <img :src="Logo" alt="Logo" class="h-24" />

          <div class="flex items-start text-center gap-4">
            <img :src="Auriculares" alt="Logo" class="w-16 h-18" />
            <p class="text-white bold flex flex-col gap-2 text-left">
              <span class="font-bold">
                ¿Necesitas ayuda? <br />
                Estamos disponibles
              </span>
              <span>
                24/7 en el 991 375 813 <br />
                (Central telefonica)
              </span>
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:col-span-2 lg:grid-cols-3 text-center gap-4 sm:pt-10 text-lg">
          <div class=" flex flex-col gap-4 items-center md:items-start text-left ">
            <p class="font-bold text-white w-full text-xl">INFORMACIÓN</p>

            <ul class="space-y-2 w-full">
              <li>
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right text-xl"></i>
                  Quienes somos
                </a>
              </li>

              <li>
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right"></i>
                  Preguntas frecuentes
                </a>
              </li>

              <li>
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right"></i>
                  Política de privacidad
                </a>
              </li>
              <li>
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right"></i>
                  Políticas de envío
                </a>
              </li>
              <li>
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right"></i>
                  Politica de garantia y devoluciones
                </a>
              </li>
              <li>
                <a href="https://forms.office.com/r/FbxkYWkiFp" target="_blank" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right"></i>
                  Libro de reclamaciones
                </a>
              </li>
            </ul>
          </div>

          <div class="flex flex-col gap-4 items-center md:items-start text-justify ">
            <p class="font-bold text-white w-full text-xl">HORARIO DE ATENCIÓN</p>

            <ul class="space-y-2 sm:space-y-3 text-md sm:text-justify text-white w-full">
              <li clas>
                <p class="font-bold">Lunes a Sábado</p>
                <p>8:00 am - 1 :00 pm y</p>
                <p>3:00 pm a 8:00 p.m</p>

              </li>

              <li class="font-medium">
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right text-md"></i>
                  Contáctanos
                </a>
              </li>

              <li class="font-medium">
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right text-md"></i>
                  Forma de Despacho
                </a>
              </li>
              <li class="font-medium">
                <a href="#" class="text-white transition hover:opacity-75"
                  ><i class="mdi mdi-chevron-right text-md"></i>
                  Modalidades de Pago
                </a>
              </li>
            </ul>
          </div>

          <div class="text-justify flex flex-col items-center gap-4">
            <p class="font-bold text-white w-full text-xl">DIRECCIÓN</p>

            <ul class="space-y-2 sm:space-y-4 text-md text-white flex flex-col md:items-start w-full">
              <li class="hover:text-plomoclaro">
                <a target="_blank" href="https://www.google.com/maps/place/GRUPO+ALMERCO+EIRL+:+www.grupoalmerco.com.pe/@-9.9270114,-76.2426815,239m/data=!3m1!1e3!4m6!3m5!1s0x91a7c31de50fed27:0x538db25c654ff53b!8m2!3d-9.927218!4d-76.241568!16s%2Fg%2F11j48j58w5?entry=ttu" class="font-bold cursor-pointer">Jr. Huallayco 1135 - Huánuco</a>
              </li>
              <li>
                <p class="font-bold">Email</p>
                <a href="#" class="text-white transition hover:opacity-75">ventas@grupoalmerco.com.pe
                </a>
              </li>
              <li class="flex justify-center gap-5">
                <a target="_blank" class="h-12 w-12 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.facebook.com/GrupoalmercoPeru/">
                  <img :src="facebook" alt="Logo" />
                </a>
                <a target="_blank" class="h-12 w-12 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://wa.me/51991375813?text=¡Hola!%20Quiero%20recibir%20mas%20información%20y%20promociones." >
                  <img :src="whatsapp" alt="Logo" />
                </a>
                <a target="_blank" class="h-12 w-12 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.tiktok.com/@grupo_almerco">
                  <img :src="tiktok" alt="Logo" />
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="lg:hidden border-t border-white flex flex-col gap-4  justify-between text-white text-center items-center w-full p-4">
      <div class="flex flex-col gap-2 p-2 font-bold items-center md:flex-row">
        <a href="" class="w-16"><img :src="bcp" alt="Logo" /></a>
        <a href="" class="w-16"><img :src="bbva" alt="Logo" /></a>
        <a href="" class="w-16"><img :src="visa" alt="Logo" /></a>
        <a href="" class="w-32"><img :src="interbank" alt="Logo" /></a>
      </div>
      <p class="text-xs">
        <span class="font-bold">GRUPO ALMERCO © 2024</span> - TODOS LOS DERECHOS
        RESERVADOS
      </p>
    </div>
    <div class="hidden border-t border-white lg:flex justify-between text-white text-center items-center w-full p-2 lg:px-10 xl:px-32">
        <p class="text-xs lg:text-md xl:text-lg">
          <span class="font-bold">GRUPO ALMERCO © 2024</span> - TODOS LOS DERECHOS
          RESERVADOS
        </p>
        <div class="flex flex-col gap-2 sm:flex-row p-2 sm:gap-4 font-bold items-center">
          <a href="" class="w-16"><img :src="bcp" alt="Logo" /></a>
          <a href="" class="w-16"><img :src="bbva" alt="Logo" /></a>
          <a href="" class="w-16"><img :src="visa" alt="Logo" /></a>
          <a href="" class="w-32"><img :src="interbank" alt="Logo" /></a>
        </div>
      </div>
  </footer>
</template>
