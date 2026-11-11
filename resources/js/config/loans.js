/**
 * Loan catalog values. They are persisted as-is, so every loan screen
 * (forms, detail, external view) must read them from here.
 */

export const LOAN_TYPES = [
    { value: 'Otorgado', label: 'Otorgado', hint: 'Prestaste dinero a alguien' },
    { value: 'Recibido', label: 'Recibido', hint: 'Alguien te prestó dinero' },
];

export const LOAN_TYPE_VALUES = LOAN_TYPES.map((type) => type.value);

export const LOAN_PERIODICITIES = [
    { value: 'A un pago', label: 'A un pago', hint: 'Una sola exhibición' },
    { value: 'Todos los días', label: 'Todos los días', hint: 'Cada 24 horas' },
    { value: 'Semanal', label: 'Semanal', hint: 'Cada 7 días' },
    { value: 'Quincenal', label: 'Quincenal', hint: 'Cada 15 días' },
    { value: 'Mensual', label: 'Mensual', hint: 'Una vez al mes' },
    { value: 'Anual', label: 'Anual', hint: 'Una vez al año' },
];

export const LOAN_PERIODICITY_VALUES = LOAN_PERIODICITIES.map((periodicity) => periodicity.value);

export const LOAN_PROFITABILITY_MODES = [
    { value: 'Porcentaje', label: 'Porcentaje', symbol: '%' },
    { value: 'Cantidad', label: 'Cantidad fija', symbol: '$' },
];

export const LOAN_PROFITABILITY_TYPES = ['Amortizable'];

export const LOAN_STATUSES = [
    { value: 'En curso', label: 'En curso' },
    { value: 'Pagado', label: 'Pagado' },
];

export const LOAN_STATUS_VALUES = LOAN_STATUSES.map((status) => status.value);

export const LOAN_PAYMENT_METHODS = [
    { value: 'Efectivo', label: 'Efectivo', icon: 'cash' },
    { value: 'Transferencia', label: 'Transferencia', icon: 'transfer' },
    { value: 'Depósito', label: 'Depósito', icon: 'deposit' },
];

export const LOAN_PAYMENT_METHOD_VALUES = LOAN_PAYMENT_METHODS.map((method) => method.value);
