/**
 * Canonical income catalogs.
 * Values are persisted as-is, so every screen (forms, filters, dashboard)
 * must read them from here to stay in sync.
 */

export const INCOME_CATEGORIES = [
    { value: 'Ventas', label: 'Ventas', hint: 'Productos y servicios cobrados' },
    { value: 'Intereses', label: 'Intereses', hint: 'Rendimientos de inversiones' },
    { value: 'Nómina', label: 'Nómina', hint: 'Sueldo o salario' },
    { value: 'Prestación de servicios', label: 'Servicios', hint: 'Trabajo independiente' },
    { value: 'Comisión', label: 'Comisión', hint: 'Comisiones por venta' },
    { value: 'Renta', label: 'Renta', hint: 'Alquiler de un inmueble' },
    { value: 'Otro', label: 'Otro', hint: 'Cualquier otro ingreso' },
];

export const INCOME_PAYMENT_METHODS = [
    { value: 'Efectivo', label: 'Efectivo', icon: 'cash' },
    { value: 'Transferencia', label: 'Transferencia', icon: 'transfer' },
    { value: 'Depósito', label: 'Depósito', icon: 'deposit' },
    { value: 'Cheque', label: 'Cheque', icon: 'cheque' },
    { value: 'Pago con tarjeta', label: 'Pago con tarjeta', icon: 'card' },
];

export const INCOME_PERIODICITIES = [
    { value: 'Todos los días', label: 'Todos los días', hint: 'Cada 24 horas' },
    { value: 'Semanal', label: 'Semanal', hint: 'Cada 7 días' },
    { value: 'Mensual', label: 'Mensual', hint: 'Una vez al mes' },
    { value: 'Anual', label: 'Anual', hint: 'Una vez al año' },
];

export const INCOME_CATEGORY_VALUES = INCOME_CATEGORIES.map((category) => category.value);
export const INCOME_PAYMENT_METHOD_VALUES = INCOME_PAYMENT_METHODS.map((method) => method.value);
export const INCOME_PERIODICITY_VALUES = INCOME_PERIODICITIES.map((periodicity) => periodicity.value);

/**
 * Outcome (expense) catalogs. Payment methods and periodicities are shared
 * with incomes so both modules stay consistent.
 */
export const OUTCOME_CATEGORIES = [
    { value: 'Alimentos y bebidas', label: 'Alimentos y bebidas', hint: 'Súper, restaurantes, café' },
    { value: 'Transporte', label: 'Transporte', hint: 'Gasolina, transporte público' },
    { value: 'Servicios', label: 'Servicios', hint: 'Luz, agua, internet, renta' },
    { value: 'Compras', label: 'Compras', hint: 'Ropa, tecnología, hogar' },
    { value: 'Salud y bienestar', label: 'Salud y bienestar', hint: 'Farmacia, consultas, gym' },
    { value: 'Entretenimiento', label: 'Entretenimiento', hint: 'Streaming, salidas, viajes' },
    { value: 'Educación y desarrollo personal', label: 'Educación', hint: 'Cursos, colegiaturas, libros' },
    { value: 'Otro', label: 'Otro', hint: 'Cualquier otro gasto' },
];

export const OUTCOME_PAYMENT_METHODS = INCOME_PAYMENT_METHODS;
export const OUTCOME_PERIODICITIES = INCOME_PERIODICITIES;

export const OUTCOME_CATEGORY_VALUES = OUTCOME_CATEGORIES.map((category) => category.value);
