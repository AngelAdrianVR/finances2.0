<template>

    <Head title="Bienvenido" />
    <header class="flex itemscenter justify-between bg-[#043C3A] px-5 py-4 sticky top-0 left-0 w-full z-20">
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
        <button @click="$inertia.visit(route('login'))" type="button" class="z-10 normal-button">
            Iniciar sesión
        </button>
    </header>
    <main class="relative">
        <section class="hero text-center relative px-3">
            <h1 class="text-2xl font-bold text-white pt-2">CONTROLA TUS FINANZAS DE MANERA FÁCIL</h1>
            <figure class="mt-3">
                <img src="@/../../public/images/hero1mobile.png" class="select-none" :draggable="false"
                    alt="Smartphone con imagenes de graficas">
            </figure>
            <p class="mt-8 text-[#585858]">Crea tus ingresos, gastos, préstamos y más desde un solo lugar</p>
            <button @click="$inertia.visit(route('register'))" type="button"
                class="futuristic-button mt-10 ml-auto">
                Crear cuenta gratis
            </button>
        </section>
    </main>
</template>
<script>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {

        }
    },
    components: {
        Head,
        ApplicationMark
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
    padding: 10px 16px;
    font-size: 14px;
    color: #fff;
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
    font-size: 12px;
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
</style>