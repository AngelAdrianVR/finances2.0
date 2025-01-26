<template>

    <Head title="Bienvenido" />
    <header class="flex itemscenter justify-between bg-[#043C3A] px-10 py-4 sticky top-0 left-0 w-full z-20">
        <svg width="78" height="40" viewBox="0 0 78 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M57.8128 11.3056C57.8128 7.34127 51.2248 2.58225 45.332 0V13.3611C45.332 16.004 50.7483 20.611 57.8128 23.9325V11.3056Z"
                fill="white" />
            <path
                d="M77.782 3.23016C69.2657 5.43254 60.1621 14.8294 60.1621 17.9127V37C60.7441 36.6944 61.0666 36.5254 61.6304 36.2659C62.1444 29.5161 63.4933 26.4677 68.0911 22.3175C70.849 17.8366 72.9306 15.835 77.782 13.5079V3.23016Z"
                fill="white" />
            <path
                d="M66.4759 33.7698L63.3924 35.3849C63.7515 31.0712 64.5137 29.1855 66.6228 26.5754C66.2947 28.9928 66.1858 30.4461 66.4759 33.7698Z"
                fill="white" />
            <path
                d="M77.782 21.877C76.7269 25.856 74.7668 28.3021 67.7974 33.1825C67.2751 29.1534 67.5044 27.6155 68.3847 25.1071C70.1649 22.1055 72.345 21.0216 77.782 19.9683V21.877Z"
                fill="white" />
            <path
                d="M77.782 17.9127C74.7514 18.4833 73.289 19.1956 70.7341 20.5556C72.7721 17.6 74.4065 16.3004 77.782 14.9762V17.9127Z"
                fill="white" />
            <path
                d="M5.632 30.088V31.512H2.4L2.528 31.88V37H2.128C1.552 37 1.21067 36.9627 1.104 36.888C1.008 36.8027 0.96 36.5147 0.96 36.024V25.48H5.824V26.84H2.384L2.528 27.24V29.688L2.384 30.088H5.632ZM7.25688 36.024V25.48H7.59288C8.17954 25.48 8.52088 25.5227 8.61688 25.608C8.72354 25.6827 8.77688 25.9653 8.77688 26.456V37H8.40887C7.83288 37 7.49688 36.9627 7.40088 36.888C7.30488 36.8027 7.25688 36.5147 7.25688 36.024ZM14.4704 33.48V25.48H14.8224C15.3984 25.48 15.7344 25.5227 15.8304 25.608C15.9264 25.6827 15.9744 25.9653 15.9744 26.456V37H15.4944C14.961 37 14.6304 36.9627 14.5024 36.888C14.3744 36.8027 14.2357 36.5413 14.0864 36.104L12.0224 29.192H11.9424V36.024C11.9424 36.5147 11.8944 36.8027 11.7984 36.888C11.7024 36.9627 11.3664 37 10.7904 37H10.4544V25.48H10.8064C11.329 25.48 11.649 25.5173 11.7664 25.592C11.8837 25.6667 12.0277 25.928 12.1984 26.376L14.3904 33.48H14.4704ZM23.0871 37C22.5431 37 22.2125 36.9573 22.0951 36.872C21.9885 36.7867 21.8871 36.504 21.7911 36.024L21.2471 33.32H19.2311L18.5751 37H18.2071C17.6951 37 17.3698 36.9733 17.2311 36.92C17.0925 36.856 17.0231 36.7173 17.0231 36.504C17.0231 36.28 17.8338 32.6053 19.4551 25.48H20.9911L23.6151 37H23.0871ZM20.1431 28.392L19.5511 31.512L19.4231 31.784H21.0711C21.0605 31.7733 21.0338 31.7253 20.9911 31.64C20.9485 31.544 20.9271 31.4907 20.9271 31.48L20.3031 28.392H20.1431ZM28.6735 33.48V25.48H29.0255C29.6015 25.48 29.9375 25.5227 30.0335 25.608C30.1295 25.6827 30.1775 25.9653 30.1775 26.456V37H29.6975C29.1642 37 28.8335 36.9627 28.7055 36.888C28.5775 36.8027 28.4388 36.5413 28.2895 36.104L26.2255 29.192H26.1455V36.024C26.1455 36.5147 26.0975 36.8027 26.0015 36.888C25.9055 36.9627 25.5695 37 24.9935 37H24.6575V25.48H25.0095C25.5322 25.48 25.8522 25.5173 25.9695 25.592C26.0868 25.6667 26.2308 25.928 26.4015 26.376L28.5935 33.48H28.6735ZM35.2423 35.576C35.8076 35.576 36.1383 35.6453 36.2343 35.784C36.3409 35.912 36.3943 36.3173 36.3943 37H31.3863V35.816L34.7143 26.84H31.3863C31.3863 26.168 31.4236 25.7787 31.4983 25.672C31.5836 25.5547 31.8663 25.496 32.3463 25.496L36.4423 25.48V26.568L33.0343 35.576H35.2423ZM43.3059 37C42.7619 37 42.4312 36.9573 42.3139 36.872C42.2072 36.7867 42.1059 36.504 42.0099 36.024L41.4659 33.32H39.4499L38.7939 37H38.4259C37.9139 37 37.5885 36.9733 37.4499 36.92C37.3112 36.856 37.2419 36.7173 37.2419 36.504C37.2419 36.28 38.0525 32.6053 39.6739 25.48H41.2099L43.8339 37H43.3059ZM40.3619 28.392L39.7699 31.512L39.6419 31.784H41.2899C41.2792 31.7733 41.2525 31.7253 41.2099 31.64C41.1672 31.544 41.1459 31.4907 41.1459 31.48L40.5219 28.392H40.3619ZM44.7963 35.416C45.8629 35.5227 46.6416 35.576 47.1323 35.576C47.6229 35.576 47.9589 35.5387 48.1403 35.464C48.3323 35.3893 48.4763 35.304 48.5723 35.208C48.6683 35.1013 48.7376 34.9467 48.7803 34.744C48.8336 34.488 48.8602 34.1733 48.8602 33.8C48.8602 33.4267 48.7696 33.1013 48.5883 32.824C48.4069 32.5467 48.2363 32.3387 48.0763 32.2C47.9269 32.0613 47.6496 31.816 47.2443 31.464C46.8389 31.112 46.5509 30.8613 46.3803 30.712C46.2203 30.552 45.9909 30.3067 45.6923 29.976C45.1483 29.3787 44.8763 28.7547 44.8763 28.104C44.8763 26.9733 44.9936 26.28 45.2283 26.024C45.5909 25.6507 45.9803 25.4373 46.3963 25.384C46.8123 25.32 47.2549 25.288 47.7243 25.288L49.9003 25.752C49.8149 26.36 49.6923 26.7173 49.5323 26.824C49.4576 26.888 49.3243 26.92 49.1323 26.92C48.9509 26.92 48.7109 26.888 48.4123 26.824L47.8043 26.712C47.1749 26.712 46.7909 26.84 46.6523 27.096C46.5669 27.2347 46.5136 27.3627 46.4923 27.48C46.4816 27.5973 46.4763 27.7733 46.4763 28.008C46.4763 28.4453 47.0149 29.1173 48.0923 30.024C48.6043 30.4293 49.0043 30.7707 49.2923 31.048C49.5803 31.3253 49.8523 31.6613 50.1083 32.056C50.3643 32.4507 50.4923 32.824 50.4923 33.176C50.4923 33.528 50.4869 33.8 50.4763 33.992C50.4763 34.1733 50.4549 34.4293 50.4123 34.76C50.3803 35.08 50.3269 35.3307 50.2523 35.512C50.1776 35.6933 50.0656 35.9013 49.9163 36.136C49.7669 36.3707 49.5856 36.5467 49.3723 36.664C48.8283 36.9627 48.1776 37.112 47.4203 37.112C46.9936 37.112 46.4976 37.064 45.9323 36.968C45.3669 36.8613 44.9883 36.7973 44.7963 36.776V35.416Z"
                fill="white" />
        </svg>
        <div class="flex items-center space-x-3">
            <button type="button" @click="scrollToTop"
                class="bg-white bg-opacity-30 border border-[#B8B8B8] rounded-full text-white px-4 py-1">Inicio</button>
            <button type="button" @click="scrollToFeatures"
                class="bg-white bg-opacity-30 border border-[#B8B8B8] rounded-full text-white px-4 py-1">Funciones</button>
        </div>
        <button @click="$inertia.visit(route('login'))" type="button" class="z-10 normal-button">
            Iniciar sesión
        </button>
    </header>
    <main class="relative">
        <section class="hero lg:px-28 relative">
            <figure class="w-1/2">
                <img src="@/../../public/images/hero1.png" class="select-none" :draggable="false"
                    alt="Smartphone con imagenes de graficas">
            </figure>
            <div class="w-1/2 absolute top-[20%] left-[40%] text-white text-end">
                <h1 class="text-6xl text-center font-bold">CONTROLA TUS FINANZAS DE MANERA FÁCIL</h1>
                <p class="mt-8 text-2xl text-center">Crea tus ingresos, gastos, préstamos y más desde un solo lugar</p>
                <button @click="$inertia.visit(route('register'))" type="button"
                    class="futuristic-button mt-32 ml-auto">
                    Crear cuenta gratis
                </button>
            </div>
            <img src="@/../../public/images/hero2.png" class="select-none absolute -top-24 right-0" :draggable="false"
                alt="">
        </section>
        <section class="lg:px-28 relative">
            <img src="@/../../public/images/decoration1.png" class="-z-10 select-none absolute -top-24 left-0"
                :draggable="false" alt="">
            <img src="@/../../public/images/decoration2.png" class="-z-10 select-none absolute bottom-24 right-0"
                :draggable="false" alt="">
            <h1 id="Features" class="font-extrabold text-5xl text-center mt-10">FUNCIONES</h1>
            <article class="grid grid-cols-2 gap-16 mt-16 items-center">
                <figure>
                    <img src="@/../../public/images/features1.png" class="select-none" :draggable="false"
                        alt="Smartphone con imagenes de graficas">
                </figure>
                <div class="text-center px-24">
                    <h2 class="text-5xl">Graficas intuitivas</h2>
                    <p class="mt-8 text-2xl">
                        Consulta de un vistazo tus ingresos, gastos y saldo total. Gráficos interactivos que simplifican
                        la toma de decisiones financieras.
                    </p>
                </div>
                <div class="text-center px-24">
                    <h2 class="text-5xl">Control Total de Préstamos</h2>
                    <p class="mt-8 text-2xl">
                        Registra préstamos otorgados y recibidos. Automatiza los abonos para mantener tus finanzas
                        organizadas sin esfuerzo
                    </p>
                </div>
                <figure>
                    <img src="@/../../public/images/features2.png" class="select-none" :draggable="false"
                        alt="Smartphone con imagenes de graficas">
                </figure>
                <div>
                    <figure>
                        <img src="@/../../public/images/features3.png" class="select-none" :draggable="false"
                            alt="Calendario con eventos">
                    </figure>
                    <div class="text-center px-24 mt-8">
                        <h2 class="text-5xl">Gestión desde un calendario</h2>
                        <p class="mt-8 text-2xl">
                            Planea y organiza con nuestro calendario integrado. Gestiona tus movimientos recurrentes en
                            un solo lugar.
                        </p>
                    </div>
                </div>
                <div>
                    <figure>
                        <img src="@/../../public/images/features4.png" width="70%" class="select-none"
                            :draggable="false" alt="Calendario con eventos">
                    </figure>
                    <div class="text-center px-24 mt-8">
                        <h2 class="text-5xl">Administra tus gastos e ingresos</h2>
                        <p class="mt-8 text-2xl">
                            Organiza tus movimientos financieros por categoría y mantén el control al día.
                        </p>
                    </div>
                </div>
            </article>
        </section>
        <section class="pl-6 mt-10">
            <figure class="relative">
                <Link rel="stylesheet" :href="route('register')"
                    class="w-[32%] absolute bottom-24 left-36 hover:scale-105 transition-transform duration-200">
                <img src="@/../../public/images/hero3_1.png" class="select-none" :draggable="false"
                    alt="Smartphone con imagenes de graficas">
                </Link>
                <img src="@/../../public/images/hero3.png" class="select-none" :draggable="false"
                    alt="Smartphone con imagenes de graficas">
            </figure>
        </section>
        <footer class="footer text-white text-center py-1 px-6 mt-10">
            <section class="flex space-x-2">
                <div class="self-center w-[20%] flex flex-col items-center space-y-2 py-10">
                    <svg width="110" height="58" viewBox="0 0 110 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M81.7598 15.9884C81.7598 10.3821 72.443 3.65184 64.1094 0V18.8954C64.1094 22.6329 71.7691 29.1482 81.7598 33.8456V15.9884Z"
                            fill="white" />
                        <path
                            d="M110 4.56812C97.9566 7.68274 85.0822 20.9718 85.0822 25.3323V52.3257C85.9053 51.8935 86.3614 51.6545 87.1587 51.2875C87.8855 41.7419 89.7932 37.4308 96.2954 31.5615C100.196 25.2247 103.139 22.394 110 19.103V4.56812Z"
                            fill="white" />
                        <path
                            d="M94.0112 47.7576L89.6506 50.0416C90.1584 43.9412 91.2362 41.2744 94.2189 37.5831C93.7549 41.0018 93.6009 43.0571 94.0112 47.7576Z"
                            fill="white" />
                        <path
                            d="M110 30.9386C108.508 36.5657 105.736 40.0251 95.8801 46.927C95.1415 41.2289 95.4658 39.0541 96.7107 35.5067C99.2282 31.2618 102.311 29.7289 110 28.2393V30.9386Z"
                            fill="white" />
                        <path
                            d="M110 25.3323C105.715 26.1392 103.646 27.1466 100.033 29.0698C102.915 24.89 105.227 23.0522 110 21.1795V25.3323Z"
                            fill="white" />
                        <path
                            d="M7.96482 42.3378V44.3516H3.3941L3.57512 44.872V52.1128H3.00944C2.19485 52.1128 1.71214 52.06 1.56129 51.9544C1.42552 51.8337 1.35764 51.4264 1.35764 50.7325V35.8211H8.23635V37.7444H3.37147L3.57512 38.3101V41.7721L3.37147 42.3378H7.96482ZM10.2627 50.7325V35.8211H10.7379C11.5676 35.8211 12.0503 35.8815 12.1861 36.0021C12.3369 36.1077 12.4123 36.5075 12.4123 37.2014V52.1128H11.8919C11.0773 52.1128 10.6021 52.06 10.4664 51.9544C10.3306 51.8337 10.2627 51.4264 10.2627 50.7325ZM20.4641 47.1348V35.8211H20.9619C21.7765 35.8211 22.2517 35.8815 22.3874 36.0021C22.5232 36.1077 22.5911 36.5075 22.5911 37.2014V52.1128H21.9123C21.158 52.1128 20.6904 52.06 20.5094 51.9544C20.3284 51.8337 20.1323 51.4641 19.9211 50.8457L17.0021 41.0707H16.889V50.7325C16.889 51.4264 16.8211 51.8337 16.6854 51.9544C16.5496 52.06 16.0744 52.1128 15.2598 52.1128H14.7847V35.8211H15.2825C16.0216 35.8211 16.4742 35.8739 16.6401 35.9795C16.806 36.0851 17.0097 36.4547 17.251 37.0882L20.351 47.1348H20.4641ZM32.65 52.1128C31.8807 52.1128 31.413 52.0525 31.2471 51.9318C31.0963 51.8111 30.953 51.4113 30.8172 50.7325L30.0479 46.9085H27.1968L26.2691 52.1128H25.7487C25.0246 52.1128 24.5645 52.0751 24.3684 51.9997C24.1723 51.9091 24.0742 51.713 24.0742 51.4113C24.0742 51.0946 25.2207 45.8978 27.5136 35.8211H29.6858L33.3967 52.1128H32.65ZM28.4866 39.9393L27.6494 44.3516L27.4683 44.7363H29.799C29.7839 44.7212 29.7462 44.6533 29.6858 44.5326C29.6255 44.3969 29.5953 44.3214 29.5953 44.3064L28.7129 39.9393H28.4866ZM40.5503 47.1348V35.8211H41.0481C41.8627 35.8211 42.3379 35.8815 42.4736 36.0021C42.6094 36.1077 42.6773 36.5075 42.6773 37.2014V52.1128H41.9985C41.2442 52.1128 40.7766 52.06 40.5956 51.9544C40.4145 51.8337 40.2184 51.4641 40.0073 50.8457L37.0883 41.0707H36.9752V50.7325C36.9752 51.4264 36.9073 51.8337 36.7715 51.9544C36.6358 52.06 36.1606 52.1128 35.346 52.1128H34.8708V35.8211H35.3686C36.1078 35.8211 36.5604 35.8739 36.7263 35.9795C36.8922 36.0851 37.0959 36.4547 37.3372 37.0882L40.4372 47.1348H40.5503ZM49.8399 50.099C50.6394 50.099 51.107 50.197 51.2428 50.3931C51.3936 50.5741 51.4691 51.1474 51.4691 52.1128H44.3867V50.4384L49.0932 37.7444H44.3867C44.3867 36.7941 44.4395 36.2435 44.5451 36.0926C44.6658 35.9267 45.0655 35.8437 45.7443 35.8437L51.5369 35.8211V37.3598L46.7173 50.099H49.8399ZM61.2435 52.1128C60.4742 52.1128 60.0066 52.0525 59.8406 51.9318C59.6898 51.8111 59.5465 51.4113 59.4107 50.7325L58.6414 46.9085H55.7903L54.8626 52.1128H54.3422C53.6181 52.1128 53.158 52.0751 52.9619 51.9997C52.7658 51.9091 52.6678 51.713 52.6678 51.4113C52.6678 51.0946 53.8142 45.8978 56.1071 35.8211H58.2794L61.9902 52.1128H61.2435ZM57.0801 39.9393L56.2429 44.3516L56.0619 44.7363H58.3925C58.3774 44.7212 58.3397 44.6533 58.2794 44.5326C58.219 44.3969 58.1888 44.3214 58.1888 44.3064L57.3064 39.9393H57.0801ZM63.3512 49.8727C64.8597 50.0235 65.9609 50.099 66.6548 50.099C67.3487 50.099 67.8239 50.0462 68.0803 49.9406C68.3519 49.835 68.5555 49.7143 68.6913 49.5785C68.827 49.4277 68.9251 49.209 68.9854 48.9223C69.0609 48.5603 69.0986 48.1153 69.0986 47.5873C69.0986 47.0594 68.9704 46.5993 68.7139 46.2071C68.4575 45.8149 68.2161 45.5207 67.9898 45.3246C67.7786 45.1285 67.3864 44.7815 66.8132 44.2837C66.24 43.7859 65.8327 43.4314 65.5913 43.2203C65.3651 42.994 65.0407 42.647 64.6184 42.1794C63.849 41.3346 63.4644 40.4522 63.4644 39.532C63.4644 37.933 63.6303 36.9525 63.9622 36.5904C64.4751 36.0625 65.0257 35.7608 65.614 35.6853C66.2023 35.5948 66.8283 35.5496 67.492 35.5496L70.5694 36.2058C70.4487 37.0656 70.2752 37.571 70.0489 37.7218C69.9433 37.8123 69.7548 37.8576 69.4832 37.8576C69.2268 37.8576 68.8874 37.8123 68.465 37.7218L67.6052 37.5634C66.7152 37.5634 66.1721 37.7444 65.976 38.1065C65.8553 38.3026 65.7799 38.4836 65.7497 38.6495C65.7346 38.8155 65.7271 39.0644 65.7271 39.3962C65.7271 40.0147 66.4889 40.9651 68.0125 42.2473C68.7365 42.8205 69.3022 43.3032 69.7095 43.6954C70.1168 44.0876 70.5015 44.5628 70.8635 45.1209C71.2255 45.6791 71.4066 46.2071 71.4066 46.7049C71.4066 47.2027 71.399 47.5873 71.3839 47.8589C71.3839 48.1153 71.3538 48.4773 71.2934 48.945C71.2482 49.3975 71.1727 49.752 71.0672 50.0085C70.9616 50.2649 70.8032 50.559 70.592 50.8909C70.3808 51.2228 70.1243 51.4717 69.8227 51.6376C69.0533 52.06 68.1331 52.2712 67.0621 52.2712C66.4587 52.2712 65.7573 52.2033 64.9578 52.0675C64.1583 51.9167 63.6228 51.8262 63.3512 51.796V49.8727Z"
                            fill="white" />
                    </svg>
                    <span>Organiza, ahorra y crece</span>
                </div>
                <div class="w-[80%] flex items-end justify-between">
                    <p class="text-sm">© 2025 FinanzasDTW. Todos los derechos reservados</p>
                    <a href="https://app.dtw.com.mx" target="_blank" class="flex items-center space-x-2">
                        <small>By</small>
                        <DTWMark class="w-32" />
                    </a>
                </div>
            </section>
        </footer>
    </main>
</template>
<script>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import DTWMark from '@/Components/MyComponents/DTWMark.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    data() {
        return {

        }
    },
    components: {
        Head,
        ApplicationMark,
        Link,
        DTWMark,
    },
    methods: {
        // funcion que me lleva al principio de la pagina de manera suave
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        // funcion que me lleva al elemento con id "Features" de manera suave
        scrollToFeatures() {
            const element = document.getElementById('Features');
            element.scrollIntoView({
                behavior: 'smooth'
            });
        }
    }
}
</script>
<style scoped>
.hero {
    background: linear-gradient(to bottom, #043C3A, #FFFFFF);
}

/* From Uiverse.io by vanaparthyguptha */
.futuristic-button {
    position: relative;
    padding: 15px 30px;
    font-size: 14px;
    color: #fff;
    letter-spacing: 2px;
    background: linear-gradient(90deg, #75b4b5, #296A6B);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    overflow: hidden;
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease,
        background 0.4s ease;
}

.futuristic-button::before {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle,
            rgba(255, 255, 255, 0.4),
            transparent 70%);
    transform: rotate(45deg);
    transition: transform 0.5s ease;
    z-index: -1;
}

.futuristic-button:hover::before {
    transform: translateX(50%) translateY(50%);
}

.futuristic-button::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 30px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease;
    transform: scale(0.95);
    z-index: -2;
}

.futuristic-button:hover {
    transform: scale(1.05);
    background: linear-gradient(90deg, #3e8586, #1a5354);
}

.futuristic-button:hover::after {
    transform: scale(1.1);
}

.futuristic-button:active {
    transform: scale(0.98);
}


/* From Uiverse.io by adamgiebl */
.normal-button {
    --green: #fff;
    font-size: 15px;
    padding: 0.6em 2em;
    letter-spacing: 0.06em;
    position: relative;
    font-family: inherit;
    border-radius: 99px;
    overflow: hidden;
    transition: all 0.3s;
    line-height: 1.4em;
    border: 2px solid var(--green);
    background: linear-gradient(to right, rgba(27, 253, 156, 0.1) 1%, transparent 40%, transparent 60%, rgba(27, 253, 156, 0.1) 100%);
    color: var(--green);
    box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.4), 0 0 9px 3px rgba(27, 253, 156, 0.1);
}

.normal-button:hover {
    box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.6), 0 0 9px 3px rgba(27, 253, 156, 0.2);
}

.normal-button:before {
    content: "";
    position: absolute;
    left: -4em;
    width: 4em;
    height: 100%;
    top: 0;
    transition: transform .4s ease-in-out;
    background: linear-gradient(to right, transparent 1%, rgba(27, 253, 156, 0.1) 40%, rgba(27, 253, 156, 0.1) 60%, transparent 100%);
}

.normal-button:hover:before {
    transform: translateX(15em);
}

/* clase .footer con fondo gradiente vertical, el centro es #0A2929 y los extremos son negros */
.footer {
    background: linear-gradient(to bottom, black, #0A2929, black);
}
</style>