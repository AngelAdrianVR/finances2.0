<template>
    <div class="z-50 h-64 w-56 bg-white shadow-md border border-grayD9 absolute rounded-[10px]">
        <div
            class="h-[40%] bg-gradient-to-r from-gray-200 from-5% via-gray99 via-50% to-gray-200 to-95% rounded-t-[10px]">
            <button @click="$emit('close')"
                class="absolute top-1 right-2 rounded-full hover:bg-gray-300 hover:text-red-600 size-5 text-xs text-black">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <Link :href="route('profile.show')">
            <button
                class="absolute flex items-center justify-center size-5 rounded-[5px] top-1 left-2 text-[11px] text-black bg-transparent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </button>
            </Link>
        </div>
        <figure class="size-28 rounded-[5px] bg-gray-500 absolute top-6 left-[calc(50%-3.5rem)]">
            <img :src="$page.props.auth.user.profile_photo_url" class="size-28 object-cover rounded-[5px]">
        </figure>
        <div class="flex flex-col text-black text-left mt-10 mx-7">
            <span class="text-sm text-center">{{ $page.props.auth.user.name }}</span>
            <span class="text-[10px] text-gray66">{{ $page.props.auth.user.employee_properties?.charge }}</span>
            <p class="mt-px flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-3 text-gray66">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                <span class="text-[10px]">{{ $page.props.auth.user.email ?? '-' }}</span>
            </p>
            <p class="flex items-center space-x-2 text-[10px]">
                <!-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3 text-gray66">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg> -->
                <span class="w-1/3">Disponible</span>
                <span>${{ $page.props.auth.user?.total_money?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
            <p class="flex items-center space-x-2 text-[10px]">
                <span class="w-1/3">Prestado</span>
                <span>${{ $page.props.auth.user?.total_loan?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
            <p class="flex items-center space-x-2 text-[10px]">
                <span class="w-1/3">Total</span>
                <span>
                    ${{ ($page.props.auth.user?.total_money +
                        $page.props.auth.user?.total_loan)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                </span>
            </p>
        </div>
        <form method="POST" @submit.prevent="logout"
            class="text-red-500 flex mr-3 mt-2 justify-end text-redpad text-xs text-right">
            <button class="flex items-center space-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</template>
<script>
import { Link } from "@inertiajs/vue3"

export default {
    emits: ['close'],
    components: {
        Link,
    },
    methods: {
        logout() {
            this.$inertia.post(route('logout'));
        },
    },
}
</script>