<script setup>
import { computed, ref, watch } from 'vue';
import { ArrowLeft, ArrowRight, Calendar } from '@element-plus/icons-vue';

const props = defineProps({
    periodicity: { type: String, default: 'Mensual' },
    period: { type: [Date, String, null], default: null },
});

const emit = defineEmits(['update:periodicity', 'update:period', 'change']);

const options = [
    { value: 'Por día', label: 'Día', picker: 'date', step: 1, unit: 'day', format: 'DD MMM YYYY' },
    { value: 'Semanal', label: 'Semana', picker: 'week', step: 7, unit: 'day', format: '[Sem] ww · YYYY' },
    { value: 'Mensual', label: 'Mes', picker: 'month', step: 1, unit: 'month', format: 'MMMM YYYY' },
    { value: 'Anual', label: 'Año', picker: 'year', step: 1, unit: 'year', format: 'YYYY' },
];

const localPeriodicity = ref(props.periodicity);
const localPeriod = ref(props.period ? new Date(props.period) : new Date());

const current = computed(
    () => options.find((option) => option.value === localPeriodicity.value) ?? options[2]
);

const isCurrentPeriod = computed(() => {
    const now = new Date();
    const date = new Date(localPeriod.value);

    return matchUnit(now, current.value) === matchUnit(date, current.value);
});

function matchUnit(date, option) {
    if (option.unit === 'year') return date.getFullYear();

    const normalized = new Date(date);
    normalized.setHours(0, 0, 0, 0);

    if (option.unit === 'month') {
        return `${normalized.getFullYear()}-${normalized.getMonth()}`;
    }

    return normalized.toISOString().slice(0, 10);
}

function shift(direction) {
    const updated = new Date(localPeriod.value);
    updated.setDate(updated.getDate() + current.value.step * direction);
    localPeriod.value = updated;
}

function goToToday() {
    localPeriod.value = new Date();
}

function selectPeriodicity(value) {
    if (value === localPeriodicity.value) return;
    localPeriodicity.value = value;
    localPeriod.value = new Date();
}

watch(localPeriodicity, (value) => emit('update:periodicity', value));
watch(localPeriod, (value) => emit('update:period', value));

watch(
    () => [localPeriodicity.value, localPeriod.value],
    () => emit('change')
);
</script>

<template>
    <div class="dashboard-toolbar">
        <div class="dashboard-toolbar__group">
            <button
                v-for="option in options"
                :key="option.value"
                type="button"
                class="dashboard-period-pill"
                :class="{ 'is-active': localPeriodicity === option.value }"
                @click="selectPeriodicity(option.value)"
            >
                {{ option.label }}
            </button>
        </div>

        <div class="dashboard-toolbar__nav">
            <el-button circle size="small" :icon="ArrowLeft" aria-label="Periodo anterior" @click="shift(-1)" />
            <el-date-picker
                v-model="localPeriod"
                :type="current.picker"
                :format="current.format"
                :clearable="false"
                class="dashboard-period-picker"
                :prefix-icon="Calendar"
                placeholder="Selecciona un periodo"
                size="small"
            />
            <el-button circle size="small" :icon="ArrowRight" aria-label="Periodo siguiente" @click="shift(1)" />
            <el-button
                size="small"
                class="dashboard-today-button"
                :disabled="isCurrentPeriod"
                @click="goToToday"
            >
                Hoy
            </el-button>
        </div>
    </div>
</template>
