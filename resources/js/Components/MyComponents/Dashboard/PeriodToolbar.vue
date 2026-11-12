<script setup>
import { computed, ref, watch } from 'vue';
import { ArrowLeft, ArrowRight, Calendar, Loading } from '@element-plus/icons-vue';

const props = defineProps({
    periodicity: { type: String, default: 'Mensual' },
    period: { type: [Date, String, null], default: null },
    loading: { type: Boolean, default: false },
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

const DAY_PERIODICITY = 'Por día';

const current = computed(
    () => options.find((option) => option.value === localPeriodicity.value) ?? options[2]
);

const isTodayView = computed(() => {
    if (localPeriodicity.value !== DAY_PERIODICITY) return false;

    const now = new Date();
    const date = new Date(localPeriod.value);

    return (
        now.getFullYear() === date.getFullYear() &&
        now.getMonth() === date.getMonth() &&
        now.getDate() === date.getDate()
    );
});

function shift(direction) {
    const updated = new Date(localPeriod.value);

    if (current.value.unit === 'year') {
        updated.setFullYear(updated.getFullYear() + current.value.step * direction);
    } else if (current.value.unit === 'month') {
        // Anchor to day 1 so month length differences never skip a period
        updated.setDate(1);
        updated.setMonth(updated.getMonth() + current.value.step * direction);
    } else {
        updated.setDate(updated.getDate() + current.value.step * direction);
    }

    localPeriod.value = updated;
}

function goToToday() {
    localPeriod.value = new Date();
    localPeriodicity.value = DAY_PERIODICITY;
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
    () =>
        emit('change', {
            periodicity: localPeriodicity.value,
            period: localPeriod.value,
        })
);
</script>

<template>
    <div class="dashboard-toolbar" :aria-busy="loading">
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
            <span v-if="loading" class="dashboard-toolbar__loading" role="status" aria-live="polite">
                <el-icon class="is-loading" :size="14"><Loading /></el-icon>
                <span class="dashboard-toolbar__loading-text">Actualizando…</span>
            </span>

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
                :disabled="isTodayView"
                title="Ver el día de hoy"
                @click="goToToday"
            >
                Hoy
            </el-button>
        </div>
    </div>
</template>
