<template>
    <Link class="bg-black flex items-center justify-center rounded-full !size-9 px-1" :href="'/'">
        <img class="w-2/3 object-contain" src="@/../../public/images/isologo.png" alt="">
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    data() {
        return {

        }
    },
    components: {
        Link
    }
}
</script>
