<template>
    <div class="w-full h-16 flex justify-center items-center px-1 bg-[#17141D] space-x-2">
        <div @click="$inertia.get(route('incomes.index'))"
            :class="route().current('incomes.*') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('incomes.*')" class="text-[13px] absolute -top-6 left-0">Ingresos</p>
            <div v-if="route().current('incomes.*')"
                class="-z-10 absolute -top-7 -left-[34px] w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
        </div>
        <div @click="$inertia.get(route('outcomes.index'))"
            :class="route().current('outcomes.*') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('outcomes.*')" class="text-[13px] absolute -top-6 -left-0">Gastos</p>
            <div v-if="route().current('outcomes.*')"
                class="-z-10 absolute -top-7 -left-9 w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
        </div>
        <div @click="$inertia.get(route('loans.index'))"
            :class="route().current('loans.*') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('loans.*')" class="text-[13px] absolute -top-6 -left-2">Préstamos</p>
            <div v-if="route().current('loans.*')"
                class="-z-10 absolute -top-7 -left-9 w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
            </svg>
        </div>
        <div @click="$inertia.get(route('dashboard'))"
            :class="route().current('dashboard') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('dashboard')" class="text-[13px] absolute -top-6 left-2">Inicio</p>
            <div v-if="route().current('dashboard')"
                class="-z-10 absolute -top-7 -left-[34px] w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
        </div>
        <!-- <div @click="$inertia.get(route('login'))"
            :class="route().current('login') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('login')" class="text-[13px] absolute -top-6 left-1">Invers...</p>
            <div v-if="route().current('login')"
                class="-z-10 absolute -top-7 -left-[34px] w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div> -->
        <div @click="$inertia.get(route('settings.index'))"
            :class="route().current('settings.*') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('settings.*')" class="text-[13px] absolute -top-6 -left-0">Config...
            </p>
            <div v-if="route().current('settings.*')"
                class="-z-10 absolute -top-7 -left-9 w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </div>
        <div @click="$inertia.get(route('notifications'))"
            :class="route().current('notifications') ? 'bg-gray-800 text-primary' : 'text-[#999999]'"
            class="relative flex flex-col justify-center text-center px-3 rounded-[10px] my-2 py-1">
            <p v-if="route().current('notifications')"
                class="text-center text-[13px] absolute -top-6 -left-0">Notific...</p>
            <div v-if="route().current('notifications')"
                class="-z-10 absolute -top-7 -left-9 w-28 h-28 bg-[#17141D] rounded-full"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
            </svg>
            <!-- <span v-if="getUnreadNotifications"
                class="size-4 rounded-full bg-primary text-white text-[10px] absolute top-0 right-0">{{
                    getUnreadNotifications }}</span> -->
        </div>
        <button @click="showProfileCard = !showProfileCard"
            :class="{ 'border-primary': showProfileCard }"
            class="relative size-10 flex justify-center items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-primary transition">
            <img class="size-9 p-1 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                :alt="$page.props.auth.user.name">
        </button>
        <div class="absolute -top-[260px] right-56">
            <ProfileCard @close="showProfileCard = false" v-if="showProfileCard" />
        </div>
    </div>
</template>

<script>
import ProfileCard from './ProfileCard.vue';

export default {
data() {
    return {
        showProfileCard: false,
    }
},
components:{
    ProfileCard,
}
}
</script>
