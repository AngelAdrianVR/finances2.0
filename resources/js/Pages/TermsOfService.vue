<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Coin, Files, InfoFilled, Key, Lock, WarningFilled } from '@element-plus/icons-vue';
import LegalLayout from '@/Layouts/LegalLayout.vue';

const openFaq = ref([]);

const sections = [
    { id: 'aceptacion', label: 'Aceptación de los términos' },
    { id: 'servicio', label: 'El servicio y su gratuidad' },
    { id: 'cuenta', label: 'Tu cuenta y su seguridad' },
    { id: 'uso-aceptable', label: 'Uso aceptable' },
    { id: 'informacion', label: 'Tu información financiera' },
    { id: 'propiedad', label: 'Propiedad intelectual' },
    { id: 'disponibilidad', label: 'Disponibilidad del servicio' },
    { id: 'responsabilidad', label: 'Límites de responsabilidad' },
    { id: 'cancelacion', label: 'Suspensión y cancelación' },
    { id: 'terceros', label: 'Servicios de terceros' },
    { id: 'cambios', label: 'Cambios a estos términos' },
    { id: 'ley', label: 'Ley aplicable y contacto' },
    { id: 'preguntas', label: 'Preguntas frecuentes' },
];

const highlights = [
    {
        icon: 'coin',
        title: 'Gratis, sin trucos',
        text: 'Sin suscripciones, periodos de prueba ni cargos ocultos.',
    },
    {
        icon: 'user',
        title: 'Tus datos son tuyos',
        text: 'Tú decides qué registras y puedes eliminar tu cuenta cuando quieras.',
    },
    {
        icon: 'data',
        title: 'Solo una herramienta',
        text: 'Finanzas no es un banco ni un asesor financiero.',
    },
];
</script>

<template>
    <Head title="Términos y condiciones">
        <meta
            name="description"
            content="Conoce las reglas de uso de Finanzas: una plataforma gratuita para organizar tus finanzas personales, sin suscripciones ni cargos ocultos."
        />
    </Head>

    <LegalLayout
        document-key="terms"
        eyebrow="Documento legal"
        title="Términos y condiciones"
        description="Estas reglas explican qué puedes esperar de Finanzas y qué esperamos de ti. Están escritas en lenguaje claro, sin letras chiquitas."
        updated-at="2 de noviembre de 2026"
        read-time="9 min"
        :sections="sections"
        :highlights="highlights"
        summary-note="Al crear una cuenta o usar Finanzas aceptas este documento y la política de privacidad."
    >
        <section id="aceptacion" class="legal-section">
            <h2><span class="legal-h2__num">01</span>Aceptación de los términos</h2>
            <p class="legal-lead">
                Al crear una cuenta, iniciar sesión o utilizar cualquier función de Finanzas, aceptas
                quedar vinculado por estos Términos y Condiciones.
            </p>
            <p>
                Si no estás de acuerdo con alguna parte del documento, la alternativa es simple: no
                uses la plataforma. Puedes dejar de utilizarla en cualquier momento y eliminar tu
                cuenta desde tu perfil.
            </p>
            <p>
                Estos términos aplican a todas las personas que acceden al servicio, ya sea desde la
                versión web, desde un dispositivo móvil o mediante la API.
            </p>
        </section>

        <section id="servicio" class="legal-section">
            <h2><span class="legal-h2__num">02</span>El servicio y su gratuidad</h2>
            <p>
                Finanzas es una herramienta de organización financiera personal. Te permite registrar
                ingresos, gastos y movimientos recurrentes, administrar préstamos otorgados y
                recibidos, dar seguimiento a inversiones, organizar tarjetas bancarias y visualizar tu
                panorama en un panel con gráficas y calendario.
            </p>

            <div class="legal-callout">
                <el-icon><Coin /></el-icon>
                <div>
                    <strong>Finanzas es completamente gratuita.</strong> No existen planes de pago,
                    suscripciones, periodos de prueba ni cargos ocultos. No necesitas tarjeta bancaria
                    para registrarte.
                </div>
            </div>

            <p>
                Si en el futuro incorporamos funciones opcionales de pago, lo comunicaremos con
                antelación y nunca eliminará el acceso a las funciones gratuitas que ya utilizas.
            </p>
            <p class="legal-note">
                Finanzas es un servicio independiente. No estamos afiliados a ninguna institución
                bancaria y no solicitamos credenciales de tu banca en línea.
            </p>
        </section>

        <section id="cuenta" class="legal-section">
            <h2><span class="legal-h2__num">03</span>Tu cuenta y su seguridad</h2>
            <p>
                Para usar Finanzas debes crear una cuenta con un nombre, un correo electrónico válido
                y una contraseña. Eres responsable de mantener la confidencialidad de tus credenciales
                y de toda la actividad que ocurra en tu cuenta.
            </p>
            <ul>
                <li>Usa una contraseña robusta y única para Finanzas.</li>
                <li>Activa la verificación en dos pasos desde tu perfil si manejas información sensible.</li>
                <li>No compartas tu cuenta: cada persona debe registrarse con su propio correo.</li>
                <li>
                    Si detectas un acceso no autorizado, cambia tu contraseña y escríbenos de inmediato
                    a <a href="mailto:soporte@finanzas.app">soporte@finanzas.app</a>.
                </li>
            </ul>
            <p>
                Podemos solicitar la verificación de tu correo electrónico para proteger la cuenta y
                garantizar la entrega de notificaciones.
            </p>
        </section>

        <section id="uso-aceptable" class="legal-section">
            <h2><span class="legal-h2__num">04</span>Uso aceptable</h2>
            <p>
                Finanzas está pensada para uso personal y legítimo. Al utilizarla te comprometes a no
                realizar las siguientes conductas:
            </p>
            <ul>
                <li>Usar el servicio para actividades ilegales, fraudulentas o engañosas.</li>
                <li>Intentar acceder a cuentas, datos o sistemas ajenos sin autorización.</li>
                <li>Extraer información de forma automatizada masiva (scraping) sin permiso escrito.</li>
                <li>Introducir código malicioso, sobrecargar la infraestructura o interferir con el servicio.</li>
                <li>Revender, sublicenciar o explotar comercialmente la plataforma sin autorización.</li>
                <li>Suplantar la identidad de otra persona o de Finanzas.</li>
            </ul>

            <div class="legal-callout legal-callout--warning">
                <el-icon><WarningFilled /></el-icon>
                <div>
                    El incumplimiento de estas reglas puede provocar la suspensión inmediata de la
                    cuenta, sin perjuicio de las acciones legales que correspondan.
                </div>
            </div>
        </section>

        <section id="informacion" class="legal-section">
            <h2><span class="legal-h2__num">05</span>Tu información financiera</h2>
            <p>
                Todo lo que registras en Finanzas es tuyo. No reclamamos la propiedad de tus datos
                financieros y únicamente los almacenamos y procesamos para prestarte el servicio.
            </p>
            <p>
                La exactitud de la información es responsabilidad tuya. Los cálculos, saldos y
                proyecciones que muestra la plataforma se generan a partir de los datos que tú
                capturas, por lo que deben considerarse estimaciones de apoyo y no registros
                contables oficiales.
            </p>

            <div class="legal-grid">
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Lock /></el-icon>
                        Acceso privado
                    </p>
                    <p class="legal-card__text">
                        Tus movimientos solo son visibles para ti, salvo que tú decidas compartir un
                        enlace de consulta de un préstamo.
                    </p>
                </div>
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Files /></el-icon>
                        Movimientos compartidos
                    </p>
                    <p class="legal-card__text">
                        Si vinculas a otra persona, solo se comparte lo estrictamente necesario para
                        dividir o dar seguimiento a un gasto común.
                    </p>
                </div>
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Key /></el-icon>
                        Enlaces de préstamos
                    </p>
                    <p class="legal-card__text">
                        Los enlaces externos son de solo lectura. Cualquiera con el enlace puede ver la
                        información, así que compártelo con cuidado.
                    </p>
                </div>
            </div>
        </section>

        <section id="propiedad" class="legal-section">
            <h2><span class="legal-h2__num">06</span>Propiedad intelectual</h2>
            <p>
                La marca Finanzas, el logotipo, el diseño de la interfaz, los textos, las gráficas y el
                código de la plataforma son propiedad de sus titulares y están protegidos por la
                legislación de propiedad intelectual aplicable.
            </p>
            <p>
                Te otorgamos una licencia personal, limitada, no exclusiva e intransferible para usar
                la plataforma conforme a estos términos. Esta licencia no incluye el derecho de copiar,
                modificar, distribuir ni crear obras derivadas del software o del diseño.
            </p>
            <p>
                Si nos envías comentarios o sugerencias, podremos utilizarlos para mejorar el servicio
                sin que ello genere obligación de compensación alguna.
            </p>
        </section>

        <section id="disponibilidad" class="legal-section">
            <h2><span class="legal-h2__num">07</span>Disponibilidad del servicio</h2>
            <p>
                Trabajamos para mantener Finanzas disponible de forma continua, pero el servicio se
                ofrece «tal cual» y «según disponibilidad». Pueden existir interrupciones por
                mantenimiento programado, actualizaciones, fallas técnicas o causas ajenas a nosotros.
            </p>
            <p>
                Cuando sea posible, avisaremos de los mantenimientos que afecten el acceso. No
                garantizamos que el servicio esté libre de errores ni que funcione en todos los
                dispositivos o navegadores.
            </p>
        </section>

        <section id="responsabilidad" class="legal-section">
            <h2><span class="legal-h2__num">08</span>Límites de responsabilidad</h2>
            <p>
                Finanzas es una herramienta de registro y visualización. <strong>No somos una
                institución financiera, no recibimos ni transferimos dinero y no ofrecemos asesoría
                financiera, fiscal o de inversión.</strong>
            </p>
            <p>
                Las decisiones que tomes a partir de la información mostrada en la plataforma son
                responsabilidad tuya. Te recomendamos contrastar cualquier dato relevante con tu banco,
                tu contador o un asesor certificado.
            </p>

            <div class="legal-callout legal-callout--neutral">
                <el-icon><InfoFilled /></el-icon>
                <div>
                    En la medida en que la ley lo permita, no seremos responsables por daños
                    indirectos, incidentales, lucro cesante o pérdida de información derivados del uso
                    o de la imposibilidad de usar el servicio. Nada en este documento excluye
                    responsabilidades que no puedan limitarse legalmente.
                </div>
            </div>
        </section>

        <section id="cancelacion" class="legal-section">
            <h2><span class="legal-h2__num">09</span>Suspensión y cancelación</h2>
            <p>
                Puedes cancelar tu cuenta en cualquier momento desde la sección de perfil. Antes de
                hacerlo, asegúrate de guardar la información que necesites conservar, ya que la
                eliminación es permanente.
            </p>
            <p>
                Nos reservamos el derecho de suspender o cancelar cuentas que incumplan estos términos,
                que representen un riesgo de seguridad o que sean utilizadas para fines ilícitos.
                Cuando sea razonable, notificaremos la decisión y su motivo.
            </p>
        </section>

        <section id="terceros" class="legal-section">
            <h2><span class="legal-h2__num">10</span>Servicios de terceros</h2>
            <p>
                Finanzas puede apoyarse en proveedores externos para funcionar, por ejemplo servicios
                de hospedaje, envío de correos o infraestructura en la nube. Estos proveedores tratan
                información únicamente para prestar el servicio, como se describe en la
                <a :href="route('legal.privacy')">política de privacidad</a>.
            </p>
            <p>
                También podemos incluir enlaces a sitios externos. No controlamos su contenido ni sus
                prácticas, por lo que no asumimos responsabilidad por ellos.
            </p>
        </section>

        <section id="cambios" class="legal-section">
            <h2><span class="legal-h2__num">11</span>Cambios a estos términos</h2>
            <p>
                Podemos actualizar estos términos para reflejar mejoras del servicio, cambios legales o
                nuevos requisitos. Cuando los cambios sean relevantes, lo notificaremos por correo
                electrónico o dentro de la aplicación con una antelación razonable.
            </p>
            <p>
                La fecha de «última actualización» al inicio del documento siempre indicará la versión
                vigente. Si continúas usando Finanzas después de la entrada en vigor, se entenderá que
                aceptas la nueva versión.
            </p>
        </section>

        <section id="ley" class="legal-section">
            <h2><span class="legal-h2__num">12</span>Ley aplicable y contacto</h2>
            <p>
                Estos términos se rigen por las leyes de los Estados Unidos Mexicanos. Para cualquier
                controversia, las partes se someten a los tribunales competentes de Jalisco, México,
                sin perjuicio de los derechos que la ley reconozca al consumidor.
            </p>
            <p>
                Si tienes preguntas sobre este documento, escríbenos a
                <a href="mailto:soporte@finanzas.app">soporte@finanzas.app</a>. Responderemos en un
                plazo razonable.
            </p>
        </section>

        <section id="preguntas" class="legal-section">
            <h2><span class="legal-h2__num">13</span>Preguntas frecuentes</h2>

            <div class="legal-faq">
                <el-collapse v-model="openFaq">
                    <el-collapse-item title="¿Finanzas es realmente gratis?" name="gratis">
                        <p>
                            Sí. Puedes usar todas las funciones actuales sin pagar. No pedimos tarjeta
                            bancaria ni ofrecemos planes que limiten funciones. Si en el futuro
                            existieran complementos opcionales de pago, las funciones gratuitas que ya
                            usas seguirían disponibles.
                        </p>
                    </el-collapse-item>
                    <el-collapse-item title="¿Finanzas mueve dinero de verdad?" name="dinero">
                        <p>
                            No. La plataforma es un registro personal: no conecta con tu banco, no
                            procesa pagos y no custodia fondos. Solo organiza la información que tú
                            escribes.
                        </p>
                    </el-collapse-item>
                    <el-collapse-item title="¿Venden o comparten mis datos?" name="datos">
                        <p>
                            No vendemos tu información ni la usamos para publicidad. Solo se comparte
                            con proveedores técnicos necesarios para operar el servicio. Puedes leer
                            el detalle en la <a :href="route('legal.privacy')">política de privacidad</a>.
                        </p>
                    </el-collapse-item>
                    <el-collapse-item title="¿Qué pasa si quiero dejar de usar la plataforma?" name="baja">
                        <p>
                            Puedes eliminar tu cuenta desde tu perfil. La eliminación es permanente,
                            así que te recomendamos respaldar antes la información que quieras
                            conservar.
                        </p>
                    </el-collapse-item>
                    <el-collapse-item title="¿Puedo usarla para administrar un negocio?" name="negocio">
                        <p>
                            Finanzas está diseñada para finanzas personales. Puedes registrar
                            movimientos de un emprendimiento, pero la herramienta no sustituye un
                            sistema contable ni cumple obligaciones fiscales.
                        </p>
                    </el-collapse-item>
                </el-collapse>
            </div>
        </section>

        <div class="legal-cta">
            <div>
                <p class="legal-cta__title">¿Listo para ordenar tus finanzas?</p>
                <p class="legal-cta__text">
                    Crea tu cuenta gratuita y empieza a ver con claridad hacia dónde va tu dinero.
                </p>
            </div>
            <div class="legal-cta__actions">
                <el-button class="legal-btn legal-btn--light" @click="$inertia.visit(route('register'))">
                    Crear cuenta gratis
                </el-button>
                <el-button class="legal-btn legal-btn--ghost" @click="$inertia.visit(route('legal.privacy'))">
                    Leer la política de privacidad
                </el-button>
            </div>
        </div>
    </LegalLayout>
</template>
