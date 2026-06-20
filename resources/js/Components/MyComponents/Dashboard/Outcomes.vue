<template>
    <main
        class="border border-grayD9 rounded-xl p-5 shadow hover:shadow-lg transition-all ease-linear duration-200 hover:-translate-y-1">
        <h1 class="flex justify-between text-2xl text-[#575757] font-bold my-3 ml-4">
            <span>GASTOS (${{ totalOutcomes.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }})</span>
            <button @click="$inertia.get(route('outcomes.create'))" class="flex items-center justify-center size-8 border border-gray-400 rounded-full hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
        </h1>

        <section class="sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 space-y-2 sm:space-y-0">
            <!-- Transporte -->
            <div @click="handleShowDetailOutcomes('Transporte')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#C9E7F3]">
                    <svg width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.648547 14.3094C0.471389 12.4752 0.49241 11.4667 0.648547 9.6898C0.721791 8.85624 1.54247 7.41848 2.79008 6.30964C4.14215 2.02807 5.05713 0.145479 7.40964 0H16.1981C18.1592 0.357238 18.6768 0.675978 20.7049 6.30964C22.1499 7.63663 22.7611 8.33964 22.9584 9.6898C23.0801 11.4937 23.0882 12.5056 22.9584 14.3094C23.0043 15.5737 22.1697 15.9994 20.8176 15.9994H2.90275C1.26984 15.9754 0.835453 15.5753 0.648547 14.3094Z"
                            fill="#145A85" />
                        <path
                            d="M1.19863 4.7322H3.00138V3.71815C3.00138 3.49281 1.76199 2.92945 1.19863 3.60548C1.01106 3.83082 0.861596 4.50685 1.19863 4.7322Z"
                            fill="#145A85" />
                        <path
                            d="M20.382 4.7322V3.71815C20.382 3.38013 21.6739 3.17823 22.2974 3.60548C22.624 3.82934 22.7362 4.39267 22.2974 4.7322H20.382Z"
                            fill="#145A85" />
                        <path
                            d="M4.93186 16.1261C2.79012 16.1942 1.37429 16.3258 0.875669 15.6754V17.3655C1.21369 18.1542 4.70652 18.2669 4.93186 17.3655V16.1261Z"
                            fill="#145A85" />
                        <path
                            d="M22.9584 15.6754C22.4079 16.1927 21.4435 16.275 18.6779 16.1261V17.3655C18.9032 18.1542 22.6214 18.1542 22.9584 17.3655V15.6754Z"
                            fill="#145A85" />
                        <path
                            d="M19.3558 9.23897C17.3134 9.89429 16.3226 10.9291 16.4263 11.4924C16.6206 12.547 19.1289 12.7318 20.1445 12.3938C21.4984 11.9431 22.2531 11.0418 22.0599 10.3657C21.7429 9.25634 20.4826 8.87748 19.3558 9.23897Z"
                            fill="#C9E7F3" />
                        <path
                            d="M4.14515 9.23897C6.02251 9.66393 7.07462 10.5911 7.07462 11.4924C7.07462 12.0558 5.7014 12.9643 4.14515 12.6192C3.12897 12.3938 1.30866 11.1544 1.55369 10.3657C1.79873 9.57694 2.26779 8.81401 4.14515 9.23897Z"
                            fill="#C9E7F3" />
                        <path
                            d="M14.3961 12.1685H9.77656C8.28163 12.3187 8.31091 14.4655 9.77656 14.6473H14.3961C15.8608 14.6473 15.7482 12.1685 14.3961 12.1685Z"
                            fill="#C9E7F3" />
                        <path
                            d="M19.5 6.3095H3.82812C4.7295 2.36595 6.25896 0.887169 7.49835 0.999841L15.9984 1C17.3504 1 18.486 2.25328 19.5 6.3095Z"
                            fill="#C9E7F3" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Transporte</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        transport.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Compras -->
            <div @click="handleShowDetailOutcomes('Compras')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F3D4C9]">
                    <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13.6262 9.25V4.75C13.6262 3.75544 13.2311 2.80161 12.5279 2.09835C11.8246 1.39509 10.8708 1 9.87621 1C8.88164 1 7.92782 1.39509 7.22456 2.09835C6.52129 2.80161 6.12621 3.75544 6.12621 4.75V9.25M17.4822 7.257L18.7452 19.257C18.8152 19.922 18.2952 20.5 17.6262 20.5H2.12621C1.96841 20.5002 1.81235 20.4671 1.66815 20.4031C1.52396 20.339 1.39485 20.2453 1.28923 20.1281C1.1836 20.0108 1.10382 19.8727 1.05506 19.7226C1.0063 19.5726 0.989655 19.4139 1.00621 19.257L2.27021 7.257C2.29936 6.98056 2.42983 6.7247 2.63646 6.53876C2.84308 6.35281 3.11123 6.24995 3.38921 6.25H16.3632C16.9392 6.25 17.4222 6.685 17.4822 7.257ZM6.50121 9.25C6.50121 9.34946 6.4617 9.44484 6.39137 9.51517C6.32104 9.58549 6.22566 9.625 6.12621 9.625C6.02675 9.625 5.93137 9.58549 5.86104 9.51517C5.79071 9.44484 5.75121 9.34946 5.75121 9.25C5.75121 9.15054 5.79071 9.05516 5.86104 8.98483C5.93137 8.91451 6.02675 8.875 6.12621 8.875C6.22566 8.875 6.32104 8.91451 6.39137 8.98483C6.4617 9.05516 6.50121 9.15054 6.50121 9.25ZM14.0012 9.25C14.0012 9.34946 13.9617 9.44484 13.8914 9.51517C13.821 9.58549 13.7257 9.625 13.6262 9.625C13.5267 9.625 13.4314 9.58549 13.361 9.51517C13.2907 9.44484 13.2512 9.34946 13.2512 9.25C13.2512 9.15054 13.2907 9.05516 13.361 8.98483C13.4314 8.91451 13.5267 8.875 13.6262 8.875C13.7257 8.875 13.821 8.91451 13.8914 8.98483C13.9617 9.05516 14.0012 9.15054 14.0012 9.25Z"
                            stroke="#E36C12" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Compras</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        purchases.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Salud y bienestar -->
            <div @click="handleShowDetailOutcomes('Salud y bienestar')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#D4F3C9]">
                    <svg width="30" height="23" viewBox="0 0 30 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.7734 0C19.9325 0 18.2516 0.685417 16.9721 1.81498C16.0725 2.63181 15.542 3.18625 14.5156 4.46635C13.4744 3.03566 12.9582 2.55363 12.0591 1.81498C10.7796 0.685417 9.09876 0 7.25781 0C3.24943 0 0 3.24943 0 7.25781C0 8.42145 0.315293 9.60264 0.760606 10.4959C1.20592 11.3892 6.49335 18.8295 14.5156 23.0017C21.7107 19.7036 27.7334 11.4111 28.2706 10.4959C28.8079 9.58069 29.0312 8.42145 29.0312 7.25781C29.0312 5.09038 28.0812 3.14486 26.5748 1.81498C25.2953 0.685417 23.6144 0 21.7734 0Z"
                            fill="#2E8A14" />
                        <path d="M0.855469 11.9541H10.8741L12.6957 5.97705L17.2496 17.9311L19.9819 11.9541H28.179"
                            stroke="#D4F3C9" stroke-width="0.84557" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Salud y bienestar</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        health.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Entretenimiento -->
            <div @click="handleShowDetailOutcomes('Entretenimiento')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F1C9F3]">
                    <svg width="33" height="16" viewBox="0 0 33 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.4375 10.9885H14.1451L14.0018 11.2433C12.765 13.4412 10.4113 14.9239 7.71196 14.9239C3.7289 14.9239 0.5 11.695 0.5 7.71196C0.5 3.7289 3.7289 0.5 7.71196 0.5H16.5H25.288C29.2711 0.5 32.5 3.7289 32.5 7.71196C32.5 11.695 29.2711 14.9239 25.288 14.9239C22.5887 14.9239 20.235 13.4412 18.9982 11.2433L18.8549 10.9885H18.5625H16.5H14.7065H14.4375Z"
                            stroke="#57145A" />
                        <path
                            d="M26.7242 4.75258C26.7242 5.49546 26.122 6.09769 25.3791 6.09769C24.6362 6.09769 24.034 5.49546 24.034 4.75258C24.034 4.0097 24.6362 3.40747 25.3791 3.40747C26.122 3.40747 26.7242 4.0097 26.7242 4.75258Z"
                            fill="#57145A" />
                        <path
                            d="M29.4144 7.4428C29.4144 8.18568 28.8122 8.78791 28.0693 8.78791C27.3264 8.78791 26.7242 8.18568 26.7242 7.4428C26.7242 6.69991 27.3264 6.09769 28.0693 6.09769C28.8122 6.09769 29.4144 6.69991 29.4144 7.4428Z"
                            fill="#57145A" />
                        <path
                            d="M26.7242 10.133C26.7242 10.8759 26.122 11.4781 25.3791 11.4781C24.6362 11.4781 24.034 10.8759 24.034 10.133C24.034 9.39013 24.6362 8.78791 25.3791 8.78791C26.122 8.78791 26.7242 9.39013 26.7242 10.133Z"
                            fill="#57145A" />
                        <path
                            d="M24.034 7.4428C24.034 8.18568 23.4317 8.78791 22.6889 8.78791C21.946 8.78791 21.3438 8.18568 21.3438 7.4428C21.3438 6.69991 21.946 6.09769 22.6889 6.09769C23.4317 6.09769 24.034 6.69991 24.034 7.4428Z"
                            fill="#57145A" />
                        <path
                            d="M7.0822 3.40747C6.33932 3.40747 5.73709 4.0097 5.73709 4.75258V6.09769H4.39198C3.6491 6.09769 3.04688 6.69991 3.04688 7.4428C3.04688 8.18568 3.6491 8.78791 4.39198 8.78791H5.73709V10.133C5.73709 10.8759 6.33932 11.4781 7.0822 11.4781C7.82508 11.4781 8.42731 10.8759 8.42731 10.133V8.78791H9.77242C10.5153 8.78791 11.1175 8.18568 11.1175 7.4428C11.1175 6.69991 10.5153 6.09769 9.77242 6.09769H8.42731V4.75258C8.42731 4.0097 7.82508 3.40747 7.0822 3.40747Z"
                            fill="#57145A" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Entretenimiento</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        entretainment.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Alimentos y bebidas -->
            <div @click="handleShowDetailOutcomes('Alimentos y bebidas')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F3EAC9]">
                    <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22.2829 12.9668H4.52467C3.80558 12.8856 3.35935 12.745 2.85648 12.3887C3.3469 12.7166 3.95309 12.8851 4.52467 12.9668H22.2829C23.5382 12.7291 24.2605 12.485 24.7284 12.0562C24.196 12.59 23.5253 12.7711 22.2829 12.9668Z"
                            fill="#2BBE7D" />
                        <path
                            d="M4.67943 8.11C2.85808 10.3867 3.02098 11.8062 6.04545 11.4492M6.50079 5.37797V5.07441C6.61934 5.07441 6.6858 5.07441 6.80435 5.07441V4.77085M8.62571 3.55661C8.62571 3.43807 8.62571 3.3716 8.62571 3.25305M9.53639 5.37797C9.65493 5.37797 9.7214 5.37797 9.83994 5.37797M9.83994 5.07441C9.95849 5.07441 10.025 5.07441 10.1435 5.07441M11.6613 2.94949C11.7798 2.94949 11.8463 2.94949 11.9649 2.94949V2.64593M15.0005 2.64593H15.304C15.304 2.82376 15.304 2.92345 15.304 3.10127M17.1254 3.55661C17.3625 3.55661 17.4954 3.55661 17.7325 3.55661M19.5538 4.77085H19.2503C19.2503 4.6523 19.2503 4.58584 19.2503 4.46729M16.2147 5.07441C16.3332 5.19296 16.5182 5.37797 16.5182 5.37797M13.7862 5.37797C13.7269 5.3187 13.6344 5.22619 13.6344 5.22619M7.10791 11.4492H7.25969M3.00826 14.94L3.1609 16.7614C3.47073 17.444 3.82331 17.6663 4.52626 17.8239M3.00826 14.94C0.411292 15.1481 0.303723 14.6249 2.85648 12.3887M3.00826 14.94C3.69221 14.8803 3.97237 15.0334 4.6771 15.6989C5.60481 16.6906 6.14351 16.4502 7.10557 15.6989C8.35823 14.4989 8.89899 14.3513 9.68583 15.2435C10.3846 15.936 10.8953 15.9897 11.8107 15.3953C12.7966 14.6523 13.4237 14.6897 14.5428 15.3953C15.9311 16.5423 16.7039 16.4735 18.0337 15.6989C19.4386 14.6385 19.9865 14.5617 20.7657 15.0917C21.5856 15.7394 22.0361 15.8303 22.7389 15.0917C23.4405 14.5694 23.7459 14.4543 24.4085 14.6364M4.52626 17.8239H22.4369M4.52626 17.8239C1.03598 17.5203 1.18776 21.7702 4.52626 21.7702H23.3476C25.6243 21.7702 26.3832 17.5203 23.3476 17.5203L22.4369 17.8239M22.4369 17.8239C24.5691 16.7859 24.9689 16.1427 24.4085 14.6364M24.4085 14.6364C26.2298 15.0917 26.6852 13.7257 24.7284 12.0562M4.52467 12.9668H22.2829M4.52467 12.9668C3.95309 12.8851 3.3469 12.7166 2.85648 12.3887M4.52467 12.9668C3.80558 12.8856 3.35935 12.745 2.85648 12.3887M22.2829 12.9668C23.5382 12.7291 24.2605 12.485 24.7284 12.0562M22.2829 12.9668C23.5253 12.7711 24.196 12.59 24.7284 12.0562M2.85648 12.3887C2.31763 11.9772 2.15989 11.6941 1.94601 11.1455C1.87039 10.302 1.90817 9.89754 1.94601 9.32417C2.85808 5.52968 7.25969 1.43163 10.4457 1.12807C12.5041 0.943426 13.6271 0.971783 15.6062 1.12807C19.7042 1.58341 23.8023 4.92256 25.3201 9.02061C25.3848 9.84771 25.4889 10.3166 25.3201 11.1455C25.2195 11.6392 25.2384 11.5889 24.7284 12.0562"
                            stroke="#B19A18" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Alimentos y bebidas</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        food.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Servicios -->
            <div @click="handleShowDetailOutcomes('Servicios')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#C9F3F0]">
                    <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.30262 21.7348V11.3028H1.67485C0.669061 11.3028 0.529547 9.88183 1.67371 8.59949L4.96619 5.93086V2.84633C4.96619 2.22249 5.07016 2.01454 5.65934 2.01454H7.25359C7.74835 1.97325 7.95665 2.08019 8.01606 2.84633V3.6088L11.7591 0.558941C12.8681 -0.168887 13.0414 -0.203539 13.9425 0.558941L24.1319 8.5302C25.8301 9.88185 24.9637 11.3028 24.1319 11.3028H22.399V21.9427C22.399 22.9131 22.3306 23.5 21.4988 23.5C20.2778 23.5 20.3635 23.3637 15.5 23.5V14.5H11V23.5H4.49878C3.21645 23.5 3.30262 22.9478 3.30262 21.7348Z"
                            fill="#147D85" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Servicios</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        services.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Educación y desarrollo personal -->
            <div @click="handleShowDetailOutcomes('Educación y desarrollo personal')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F3C9DF]">
                    <svg width="27" height="21" viewBox="0 0 27 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24.7455 7.09687V13.7131L24.6914 13.7749C24.2226 13.8826 23.8836 14.3911 23.8838 14.8997C23.8838 15.4423 24.2481 15.8999 24.7455 16.0412C23.8501 16.4836 23.7792 17.7644 23.8838 20.5782H26.4265C26.5128 17.7469 26.4818 16.3277 25.3953 16.0412C25.8927 15.8999 26.257 15.4423 26.257 14.8997C26.257 14.3803 25.9233 13.9388 25.4586 13.778V6.77187M1 6.67851L13.2046 1L25.6634 6.67851L13.2046 12.357L1 6.67851ZM5.83097 10.5L13.2046 14.0992L20.4934 10.5V17C14.9581 20.6606 11.7557 20.752 5.83097 17V10.5Z"
                            stroke="#851457" stroke-width="0.8" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Educación y desarrollo personal</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        education.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Otros -->
            <div @click="handleShowDetailOutcomes('Otro')"
                class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F3C9C9]">
                    <i class="fa-solid fa-ellipsis text-2xl text-[#851414]"></i>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Otros</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{
                        another.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
        </section>

        <!-- -------------- Show Modal starts----------------------- -->
        <DialogModal :show="showOutcomesDetailsModal" @close="showOutcomesDetailsModal = false">
            <template #title>
                <h1>Gastos de {{ categorySelected }}</h1>
            </template>

            <template #content>
                <div class="mt-4 max-h-80 overflow-auto">
                    <table v-if="outcomes.some(item => item.category === categorySelected)"
                        class="w-full mx-auto text-xs md:text-sm">
                        <thead>
                            <tr class="text-left border-b border-primary">
                                <th class="font-bold">Concepto</th>
                                <th class="font-bold">Monto</th>
                                <th class="font-bold">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-left"
                                v-for="item in outcomes.filter(item => item.category === categorySelected)" :key="item">
                                <td class="py-1">
                                    <p class="truncate w-28 md:w-44 lg:w-48">
                                        {{ item.concept }}
                                    </p>
                                </td>
                                <td class="py-1">$ {{ item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </td>
                                <td class="py-1">{{ formatDate(item.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else>
                        <p class="text-sm text-center text-gray-400 mt-4">No hay registros para mostrar</p>
                    </div>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="showOutcomesDetailsModal = false">Cerrar</PrimaryButton>
            </template>
        </DialogModal>
        <!-- --------------------------- Modal ends ------------------------------------ -->
    </main>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            transport: 0,
            purchases: 0,
            health: 0,
            entretainment: 0,
            food: 0,
            services: 0,
            education: 0,
            another: 0,

            //general
            categorySelected: null,
            showOutcomesDetailsModal: false,
        }
    },
    components: {
        PrimaryButton,
        DialogModal,
    },
    props: {
        outcomes: Array
    },
    watch: {
        outcomes: {
            immediate: true, // Ejecuta la lógica cuando `outcomes` se inicializa
            handler(newOutcomes) {
                this.calculateCategoryTotals(newOutcomes);
            },
        },
    },
    computed: {
        totalOutcomes() {
            return this.transport + this.purchases + this.health + this.entretainment + this.food + this.services + this.education + this.another;
        },
    },
    methods: {
        formatDate(date) {
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
            }
        },
        handleShowDetailOutcomes(category) {
            this.categorySelected = category;
            this.showOutcomesDetailsModal = true;
        },
        calculateCategoryTotals(outcomes) {
            // Reinicia los totales
            this.transport = 0;
            this.purchases = 0;
            this.health = 0;
            this.entretainment = 0;
            this.food = 0;
            this.services = 0;
            this.education = 0;
            this.another = 0;

            // Itera sobre los outcomes y asigna los montos según la categoría
            outcomes?.forEach((outcome) => {
                switch (outcome.category) {
                    case "Transporte":
                        this.transport += outcome.amount;
                        break;
                    case "Compras":
                        this.purchases += outcome.amount;
                        break;
                    case "Salud y bienestar":
                        this.health += outcome.amount;
                        break;
                    case "Entretenimiento":
                        this.entretainment += outcome.amount;
                        break;
                    case "Alimentos y bebidas":
                        this.food += outcome.amount;
                        break;
                    case "Servicios":
                        this.services += outcome.amount;
                        break;
                    case "Educación y desarrollo personal":
                        this.education += outcome.amount;
                        break;
                    default:
                        this.another += outcome.amount;
                        break;
                }
            });
        },
    },
}
</script>