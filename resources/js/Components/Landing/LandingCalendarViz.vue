<script setup>
import { computed, ref } from 'vue';

const WEEKDAYS = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
const YEAR = 2026;
const MONTH = 10;
const MONTH_LABEL = 'Noviembre';

const daysInMonth = new Date(YEAR, MONTH + 1, 0).getDate();
const firstWeekday = (new Date(YEAR, MONTH, 1).getDay() + 6) % 7;

const events = {
    5: { type: 'in', label: 'Sueldo', amount: 15000 },
    10: { type: 'out', label: 'Renta', amount: 4500 },
    15: { type: 'recurring', label: 'Préstamo', amount: 1200 },
    20: { type: 'out', label: 'Tarjeta', amount: 980 },
    28: { type: 'in', label: 'Freelance', amount: 3200 },
};

const cells = [];

for (let blank = 0; blank < firstWeekday; blank += 1) {
    cells.push(null);
}

for (let day = 1; day <= daysInMonth; day += 1) {
    cells.push(day);
}

const selected = ref(10);
const selectedEvent = computed(() => events[selected.value] ?? null);

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0,
});

const detailAmount = computed(() => {
    if (!selectedEvent.value) {
        return '—';
    }

    const sign = selectedEvent.value.type === 'in' ? '+' : '-';

    return `${sign}${currency.format(selectedEvent.value.amount)}`;
});
</script>

<template>
    <div class="lp-viz lp-calviz" aria-hidden="true">
        <div class="lp-viz__head">
            <span class="lp-viz__label">{{ MONTH_LABEL }} {{ YEAR }}</span>
            <div class="lp-calviz__legend">
                <span><i class="lp-calviz__dot lp-calviz__dot--in"></i>Ingreso</span>
                <span><i class="lp-calviz__dot lp-calviz__dot--out"></i>Gasto</span>
                <span><i class="lp-calviz__dot lp-calviz__dot--recurring"></i>Recurrente</span>
            </div>
        </div>

        <div class="lp-calviz__grid">
            <span v-for="(weekday, index) in WEEKDAYS" :key="`weekday-${index}`" class="lp-calviz__weekday">
                {{ weekday }}
            </span>

            <template v-for="(day, index) in cells" :key="`day-${index}`">
                <span v-if="day === null" class="lp-calviz__blank"></span>
                <span
                    v-else
                    class="lp-calviz__day"
                    :class="{
                        'is-selected': day === selected,
                        'has-event': events[day],
                        [`has-event--${events[day]?.type}`]: events[day],
                    }"
                    @mouseenter="selected = day"
                >
                    {{ day }}
                </span>
            </template>
        </div>

        <div
            class="lp-calviz__detail"
            :class="selectedEvent ? `lp-calviz__detail--${selectedEvent.type}` : 'lp-calviz__detail--empty'"
        >
            <span class="lp-calviz__detail-dot"></span>
            <span class="lp-calviz__detail-label">
                {{ selectedEvent ? selectedEvent.label : 'Sin movimientos' }}
                <small>Día {{ selected }}</small>
            </span>
            <strong class="lp-calviz__detail-amount">{{ detailAmount }}</strong>
        </div>
    </div>
</template>