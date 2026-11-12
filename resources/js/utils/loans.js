import { parseCalendarDate } from '@/utils/date';

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

export function formatCurrency(value) {
    return currency.format(Number(value ?? 0));
}

export function formatMoney(value) {
    return Number(value ?? 0).toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export function latestPayment(payments = []) {
    return payments.reduce((latest, payment) => {
        if (!latest) return payment;

        return Number(payment.id) > Number(latest.id) ? payment : latest;
    }, null);
}

export function loanRemaining(loan) {
    const lastPayment = latestPayment(loan?.payments);

    return Number(lastPayment?.remaining ?? loan?.amount ?? 0);
}

export function loanTotalCapital(loan) {
    return (loan?.payments ?? []).reduce((sum, payment) => sum + Number(payment.capital ?? 0), 0);
}

export function loanTotalInterest(loan) {
    return (loan?.payments ?? []).reduce((sum, payment) => sum + Number(payment.interest ?? 0), 0);
}

export function loanTotalPaid(loan) {
    return loanTotalCapital(loan) + loanTotalInterest(loan);
}

export function loanPaidProgress(loan) {
    const amount = Number(loan?.amount ?? 0);

    if (amount <= 0) return 0;

    return Math.min(100, Math.round((loanTotalCapital(loan) / amount) * 1000) / 10);
}

export function loanFolio(loan) {
    const prefix = loan?.type === 'Otorgado' ? 'O' : 'R';

    return `${prefix}-${String(loan?.id ?? 0).padStart(3, '0')}`;
}

export function loanPersonName(loan) {
    return (loan?.type === 'Otorgado' ? loan?.beneficiary_name : loan?.lender_name) ?? 'Sin nombre';
}

export function isLoanOverdue(loan) {
    if (!loan?.expired_date || loan?.status === 'Pagado') return false;

    const dueDate = parseCalendarDate(loan.expired_date);

    if (!dueDate) return false;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return dueDate < today;
}

export function loanStatusLabel(loan) {
    if (loan?.status === 'Pagado') return 'Pagado';

    return isLoanOverdue(loan) ? 'Vencido' : (loan?.status ?? 'En curso');
}

export function loanInterestLabel(loan) {
    const value = Number(loan?.profitability ?? 0);

    if (!value) return 'Sin intereses';

    const amount = loan?.profitability_mode === 'Porcentaje'
        ? `${formatMoney(value)}%`
        : formatCurrency(value);

    return loan?.profitability_period ? `${amount} · ${loan.profitability_period}` : amount;
}
