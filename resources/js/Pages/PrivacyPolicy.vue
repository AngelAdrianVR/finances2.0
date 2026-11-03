<script setup>
import { Head } from '@inertiajs/vue3';
import { Files, InfoFilled, Key, Lock, Service, WarningFilled } from '@element-plus/icons-vue';
import LegalLayout from '@/Layouts/LegalLayout.vue';

const sections = [
    { id: 'responsable', label: 'Responsable del tratamiento' },
    { id: 'datos', label: 'Datos que recopilamos' },
    { id: 'finalidades', label: 'Para qué usamos tus datos' },
    { id: 'base-legal', label: 'Base legal' },
    { id: 'cookies', label: 'Cookies y almacenamiento local' },
    { id: 'compartir', label: 'Con quién compartimos información' },
    { id: 'conservacion', label: 'Cuánto tiempo conservamos tus datos' },
    { id: 'seguridad', label: 'Cómo protegemos tu información' },
    { id: 'derechos', label: 'Tus derechos' },
    { id: 'menores', label: 'Menores de edad' },
    { id: 'cambios', label: 'Cambios a esta política' },
    { id: 'contacto', label: 'Contacto' },
];

const highlights = [
    {
        icon: 'lock',
        title: 'Privado por diseño',
        text: 'No vendemos tus datos ni los usamos para publicidad.',
    },
    {
        icon: 'user',
        title: 'Tú tienes el control',
        text: 'Puedes acceder, corregir o eliminar tu información.',
    },
    {
        icon: 'key',
        title: 'Seguridad primero',
        text: 'Contraseñas cifradas y verificación en dos pasos disponible.',
    },
];
</script>

<template>
    <Head title="Política de privacidad">
        <meta
            name="description"
            content="Descubre qué datos recopila Finanzas, para qué los usa y cómo puedes ejercer tus derechos de privacidad. No vendemos tu información."
        />
    </Head>

    <LegalLayout
        document-key="privacy"
        eyebrow="Privacidad y datos"
        title="Política de privacidad"
        description="Tu información financiera es personal. Aquí te explicamos, sin tecnicismos, qué datos guardamos, para qué los usamos y cómo puedes controlarlos."
        updated-at="2 de noviembre de 2026"
        read-time="8 min"
        :sections="sections"
        :highlights="highlights"
        summary-note="Usamos únicamente los datos necesarios para que Finanzas funcione."
    >
        <section id="responsable" class="legal-section">
            <h2><span class="legal-h2__num">01</span>Responsable del tratamiento</h2>
            <p class="legal-lead">
                Finanzas es responsable de los datos personales que se recopilan a través de la
                plataforma y los trata conforme a esta política y a la legislación aplicable en materia
                de protección de datos personales.
            </p>
            <p>
                Esta política aplica a la versión web de Finanzas, a su API y a cualquier
                funcionalidad asociada. No aplica a sitios de terceros a los que puedas llegar
                mediante enlaces desde la plataforma.
            </p>
            <p>
                Para cualquier asunto relacionado con tus datos personales puedes escribirnos a
                <a href="mailto:privacidad@finanzas.app">privacidad@finanzas.app</a>.
            </p>
        </section>

        <section id="datos" class="legal-section">
            <h2><span class="legal-h2__num">02</span>Datos que recopilamos</h2>
            <p>
                Solo recopilamos la información necesaria para que Finanzas funcione. No solicitamos
                datos que no necesitemos y nunca pedimos credenciales de tu banca en línea.
            </p>

            <div class="legal-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Qué incluye</th>
                            <th>¿Obligatorio?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Identificación y contacto</td>
                            <td>Nombre, correo electrónico y, si lo subes, foto de perfil.</td>
                            <td>Sí</td>
                        </tr>
                        <tr>
                            <td>Credenciales de acceso</td>
                            <td>Contraseña cifrada y, si la activas, verificación en dos pasos.</td>
                            <td>Sí</td>
                        </tr>
                        <tr>
                            <td>Información financiera</td>
                            <td>
                                Ingresos, gastos, movimientos recurrentes, préstamos, pagos e
                                inversiones que tú registras.
                            </td>
                            <td>Sí, para usar el servicio</td>
                        </tr>
                        <tr>
                            <td>Tarjetas bancarias</td>
                            <td>
                                Nombre, banco, tipo y notas descriptivas. Nunca el número completo, la
                                fecha de vencimiento ni el CVV.
                            </td>
                            <td>Opcional</td>
                        </tr>
                        <tr>
                            <td>Preferencias</td>
                            <td>Tema visual y recordatorios de pago.</td>
                            <td>Opcional</td>
                        </tr>
                        <tr>
                            <td>Datos técnicos</td>
                            <td>Fecha de registro, última actividad y tokens de API que tú generes.</td>
                            <td>Automático</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="legal-callout">
                <el-icon><Lock /></el-icon>
                <div>
                    <strong>No almacenamos datos de tu tarjeta bancaria.</strong> Los campos de tarjetas
                    son descriptivos y sirven solo para que identifiques tus cuentas dentro de la
                    plataforma.
                </div>
            </div>
        </section>

        <section id="finalidades" class="legal-section">
            <h2><span class="legal-h2__num">03</span>Para qué usamos tus datos</h2>
            <p>Utilizamos tu información exclusivamente para las siguientes finalidades:</p>
            <ul>
                <li>Crear y administrar tu cuenta, autenticarte y mantener tu sesión activa.</li>
                <li>Registrar, mostrar y calcular tus movimientos, saldos y proyecciones financieras.</li>
                <li>Generar los eventos de tu calendario y los movimientos recurrentes programados.</li>
                <li>Enviarte notificaciones relacionadas con recordatorios de pago y actividad relevante.</li>
                <li>Responder solicitudes de soporte y resolver incidencias técnicas.</li>
                <li>Mejorar la seguridad, prevenir abusos y detectar accesos no autorizados.</li>
                <li>Cumplir obligaciones legales cuando corresponda.</li>
            </ul>

            <div class="legal-callout legal-callout--neutral">
                <el-icon><InfoFilled /></el-icon>
                <div>
                    <strong>No utilizamos tu información financiera para publicidad</strong> ni la
                    vendemos, alquilamos o cedemos a terceros con fines comerciales.
                </div>
            </div>
        </section>

        <section id="base-legal" class="legal-section">
            <h2><span class="legal-h2__num">04</span>Base legal</h2>
            <p>
                Tratamos tus datos porque son necesarios para ejecutar la relación que tienes con
                nosotros al usar la plataforma, porque contamos con tu consentimiento para finalidades
                específicas —como las notificaciones por correo— y porque existen intereses legítimos
                en mantener la seguridad del servicio.
            </p>
            <p>
                Puedes retirar tu consentimiento en cualquier momento desactivando las notificaciones
                desde tu perfil o solicitando la eliminación de tu cuenta.
            </p>
        </section>

        <section id="cookies" class="legal-section">
            <h2><span class="legal-h2__num">05</span>Cookies y almacenamiento local</h2>
            <p>
                Finanzas usa cookies estrictamente necesarias para mantener tu sesión iniciada y
                proteger la plataforma contra accesos no autorizados. Sin ellas, el servicio no puede
                funcionar correctamente.
            </p>
            <p>
                También guardamos pequeñas preferencias en el almacenamiento local de tu navegador,
                como el tema claro u oscuro que elijas. Esta información permanece en tu dispositivo y
                puedes borrarla desde la configuración de tu navegador.
            </p>
            <p class="legal-note">
                No utilizamos cookies de publicidad ni herramientas de seguimiento de terceros con
                fines comerciales.
            </p>
        </section>

        <section id="compartir" class="legal-section">
            <h2><span class="legal-h2__num">06</span>Con quién compartimos información</h2>
            <p>
                Tus datos solo se comparten en los casos necesarios para operar la plataforma o cuando
                tú lo decides:
            </p>

            <div class="legal-grid">
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Service /></el-icon>
                        Proveedores técnicos
                    </p>
                    <p class="legal-card__text">
                        Hospedaje, bases de datos y envío de correos. Acceden a lo mínimo necesario y
                        bajo obligaciones de confidencialidad.
                    </p>
                </div>
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Files /></el-icon>
                        Personas que tú vinculas
                    </p>
                    <p class="legal-card__text">
                        Si compartes un gasto o vinculas una cuenta, se muestra solo la información
                        necesaria para ese movimiento.
                    </p>
                </div>
                <div class="legal-card">
                    <p class="legal-card__title">
                        <el-icon><Key /></el-icon>
                        Enlaces de consulta
                    </p>
                    <p class="legal-card__text">
                        Cuando generas un enlace externo de préstamo, cualquier persona con ese enlace
                        puede ver la información en modo lectura.
                    </p>
                </div>
            </div>

            <p class="legal-note">
                También podremos divulgar información si una autoridad competente lo requiere mediante
                un mandato legal.
            </p>
        </section>

        <section id="conservacion" class="legal-section">
            <h2><span class="legal-h2__num">07</span>Cuánto tiempo conservamos tus datos</h2>
            <p>
                Conservamos tu información mientras tu cuenta permanezca activa. Si decides eliminar tu
                cuenta, los datos personales se suprimen de nuestros sistemas y pueden permanecer en
                respaldos cifrados por un periodo limitado hasta que se rotan automáticamente.
            </p>
            <p>
                Cierta información puede conservarse por más tiempo cuando exista una obligación legal
                o sea necesaria para atender reclamaciones.
            </p>
        </section>

        <section id="seguridad" class="legal-section">
            <h2><span class="legal-h2__num">08</span>Cómo protegemos tu información</h2>
            <p>
                Aplicamos medidas técnicas y organizativas razonables para proteger tus datos, entre
                ellas:
            </p>
            <ul>
                <li>Cifrado de contraseñas; nunca las almacenamos en texto plano.</li>
                <li>Conexiones seguras mediante HTTPS en toda la plataforma.</li>
                <li>Verificación en dos pasos opcional para reforzar el acceso a tu cuenta.</li>
                <li>Tokens de API revocables y con permisos limitados.</li>
                <li>Acceso restringido a la información por usuario autenticado.</li>
            </ul>
            <div class="legal-callout legal-callout--warning">
                <el-icon><WarningFilled /></el-icon>
                <div>
                    Ningún sistema es infalible. Si detectamos un incidente que afecte tus datos,
                    notificaremos a las personas afectadas y a las autoridades cuando la ley lo exija.
                </div>
            </div>
        </section>

        <section id="derechos" class="legal-section">
            <h2><span class="legal-h2__num">09</span>Tus derechos</h2>
            <p>
                En cualquier momento puedes ejercer los siguientes derechos sobre tus datos personales:
            </p>
            <ol>
                <li>
                    <strong>Acceso:</strong> conocer qué información tenemos sobre ti y solicitar una
                    copia.
                </li>
                <li>
                    <strong>Rectificación:</strong> corregir datos inexactos o incompletos desde tu
                    perfil.
                </li>
                <li>
                    <strong>Cancelación:</strong> solicitar la eliminación de tu información cuando ya
                    no sea necesaria para el servicio.
                </li>
                <li>
                    <strong>Oposición:</strong> oponerte al tratamiento de tus datos para finalidades
                    específicas, como las notificaciones por correo.
                </li>
                <li>
                    <strong>Portabilidad:</strong> solicitar una copia de la información que hayas
                    registrado.
                </li>
            </ol>
            <p>
                Para ejercerlos, escríbenos a
                <a href="mailto:privacidad@finanzas.app">privacidad@finanzas.app</a> indicando tu
                nombre, el correo con el que te registraste y el derecho que deseas ejercer.
                Responderemos en un plazo máximo de 20 días hábiles.
            </p>
        </section>

        <section id="menores" class="legal-section">
            <h2><span class="legal-h2__num">10</span>Menores de edad</h2>
            <p>
                Finanzas no está dirigida a menores de 18 años. Si detectamos una cuenta creada por una
                persona menor de edad sin la autorización correspondiente, procederemos a su
                cancelación y a la eliminación de los datos asociados.
            </p>
        </section>

        <section id="cambios" class="legal-section">
            <h2><span class="legal-h2__num">11</span>Cambios a esta política</h2>
            <p>
                Podemos actualizar esta política para reflejar mejoras del servicio o cambios en la
                normativa. Publicaremos siempre la versión vigente en esta página con su fecha de
                actualización.
            </p>
            <p>
                Si los cambios afectan de forma significativa el tratamiento de tus datos, te
                avisaremos por correo electrónico o mediante un aviso dentro de la plataforma antes de
                que entren en vigor.
            </p>
        </section>

        <section id="contacto" class="legal-section">
            <h2><span class="legal-h2__num">12</span>Contacto</h2>
            <p>
                Si tienes dudas, comentarios o quieres ejercer tus derechos de protección de datos,
                contáctanos:
            </p>
            <ul>
                <li>Privacidad y datos personales: <a href="mailto:privacidad@finanzas.app">privacidad@finanzas.app</a></li>
                <li>Soporte general: <a href="mailto:soporte@finanzas.app">soporte@finanzas.app</a></li>
            </ul>
            <p class="legal-note">
                También puedes consultar los <a :href="route('legal.terms')">términos y condiciones</a>
                para conocer las reglas de uso de la plataforma.
            </p>
        </section>

        <div class="legal-cta">
            <div>
                <p class="legal-cta__title">Tu privacidad, sin letras chiquitas</p>
                <p class="legal-cta__text">
                    Si algo de este documento no te queda claro, escríbenos. Y si ya estás listo,
                    entra a tu panel y sigue con lo tuyo.
                </p>
            </div>
            <div class="legal-cta__actions">
                <el-button class="legal-btn legal-btn--light" @click="$inertia.visit(route('login'))">
                    Entrar a Finanzas
                </el-button>
                <el-button class="legal-btn legal-btn--ghost" @click="$inertia.visit(route('legal.terms'))">
                    Leer los términos
                </el-button>
            </div>
        </div>
    </LegalLayout>
</template>
