<script setup>
import { onMounted, ref } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: String,
    errorMessage: String,
    placeholder: String,
    autocomplete: String,
    required: Boolean,
    autofocus: Boolean,
    type: String,
    id: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-grayD9 focus:border-primary focus:ring-secondary rounded-md shadow-sm h-8 placeholder:text-gray-400 placeholder:text-sm text-sm w-full bg-transparent"
        :class="[
            {'!border-red-600' : errorMessage}, // Clases condicionadas internamente
            $attrs.class // Aquí se propagan las clases externas
        ]"
        :autocomplete="autocomplete"
        :placeholder="placeholder"
        :autofocus="autofocus"
        :required="required"
        :value="modelValue"
        :type="type"
        :id="id"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    <InputError class="mt-1" :message="errorMessage" />
</template>
