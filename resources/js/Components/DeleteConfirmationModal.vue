<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="bg-white px-4 py-5 relative">
            <button @click="close" class="size-5 flex items-center justify-center absolute top-3 right-3 rounded-full hover:bg-gray-200 hover:text-red-500">
                <i class="fa-solid fa-x text-[10px]"></i>
            </button>
            <div class="sm:flex flex-col sm:items-center">
                <div class="mx-auto shrink-0 flex items-center justify-center py-2">
                    <i class="fa-solid fa-trash-can text-3xl text-[#7A7A7A]"></i>
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
                    <h3 class="text-lg font-medium text-gray-500">
                        <slot name="title" />
                    </h3>

                    <div class="mt-4 text-sm text-gray-600">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 text-end">
            <slot name="footer" />
        </div>
    </Modal>
</template>
