# Finanzas — Project guidelines for AI assistant

## Stack

- **Backend:** Laravel 11.9, PHP ^8.2
- **Frontend:** Vue 3 (Composition API `<script setup>` preferred), Inertia.js, Vite
- **UI library:** Element Plus (always use its components — never build from scratch what Element Plus already provides). Spanish locale is configured globally.
- **Charts:** Vue3-ApexCharts
- **Date utilities:** date-fns
- **Auth:** Laravel Jetstream (with Fortify + Sanctum)
- **Routing (frontend):** Ziggy (`route()` helper)
- **Styling:** Tailwind CSS (utility classes to complement Element Plus)
- **Notifications:** Laravel database notifications (`MovementNotification`)

---

## General principles

- All code (variable names, class names, methods, routes, files) must be written in **English**
- All **user-facing text** (labels, placeholders, messages, tooltips, notifications) must be written in **Spanish** — the application UI is entirely in Spanish
  - ✅ `"Guardar cambios"`, `"Este campo es obligatorio"`, `"Nuevo ingreso"`
  - ❌ `"Save changes"`, `"This field is required"`
- Follow **SOLID principles** strictly:
  - **Single responsibility**: one class, one purpose
  - **Open/closed**: extend behavior without modifying existing code
  - **Liskov substitution**: subtypes must be replaceable for their base types
  - **Interface segregation**: prefer small, focused interfaces
  - **Dependency inversion**: depend on abstractions, not concretions
- Prefer **explicit over implicit** code — clarity over cleverness
- No commented-out dead code — delete it

---

## Architecture — target pattern

Gradually migrate toward this layered architecture. New features should follow it from the start.

```
HTTP Request
  → Controller       (thin — delegates immediately)
  → Action           (single use-case orchestrator)
  → Service          (reusable business logic)
  → Model            (rich domain logic, relationships, scopes, accessors)
  → FormRequest      (all validation lives here)
```

### Controllers — keep them thin

- One responsibility per method: receive request, call action or service, return response
- No business logic, no raw queries, no calculations
- **Target:** Use Form Requests for validation and Actions for business logic
- Always return Inertia responses (`inertia('PageName', compact('data'))`) or JSON for AJAX endpoints

```php
// ✅ Correct — thin controller delegating to Action
public function store(CreateIncomeRequest $request): RedirectResponse
{
    $this->createIncomeAction->execute($request->validated());

    return to_route('incomes.index');
}

// ❌ Avoid — business logic in controller (current pattern, migrate away)
public function store(Request $request): RedirectResponse
{
    $request->validate([...]);
    $income = Income::create([...]);
    auth()->user()->total_money += $income->amount;
    auth()->user()->save();
    // ... more logic ...
    return to_route('incomes.index');
}
```

### Actions — single use cases

- One action per use case: `CreateIncomeAction`, `ProcessLoanPaymentAction`
- Located in `app/Actions/{Module}/`
- Must have a single public method: `execute(array $data): mixed`
- Can call multiple services, fire events, dispatch jobs

```php
namespace App\Actions\Incomes;

class CreateIncomeAction
{
    public function __construct(
        private readonly IncomeService $incomeService,
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(array $data): Income
    {
        $income = $this->incomeService->create($data);
        $this->totalMoneyService->increment(auth()->user(), $income->amount);

        if ($data['is_recurring_income'] ?? false) {
            $this->calendarService->generateRecurringEvents($data);
        }

        return $income;
    }
}
```

### Services — reusable business logic

- Located in `app/Services/{Module}/`
- Handle reusable operations shared across multiple actions
- May interact with models, external APIs, or other services
- Must be injected via constructor (dependency inversion)

```php
namespace App\Services\Incomes;

class IncomeService
{
    public function create(array $data): Income
    {
        return Income::create($data + ['user_id' => auth()->id()]);
    }

    public function getTotalForPeriod(Carbon $start, Carbon $end): float
    {
        return Income::where('user_id', auth()->id())
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }
}
```

### Models — rich and expressive

- Define all relationships, scopes, accessors, mutators, and casts here
- Use `$fillable` explicitly — never `$guarded = []`
- Define casts for dates, booleans, and enums
- Add query scopes for common filters (especially `where('user_id', auth()->id())`)
- All user-scoped models have `belongsTo(User::class)`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = [
        'amount', 'concept', 'category', 'payment_method',
        'description', 'created_at', 'automatically_created', 'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'automatically_created' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
```

---

## Form requests — target pattern

- **Target:** All validation should live in Form Request classes
- Located in `app/Http/Requests/{Module}/`
- Always authorize with user checks
- Use `prepareForValidation()` to normalize data before validation

```php
namespace App\Http\Requests\Incomes;

class StoreIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // All authenticated users can create
    }

    public function rules(): array
    {
        return [
            'amount'         => ['required', 'numeric', 'min:0', 'max:999999'],
            'concept'        => ['required', 'string', 'max:50'],
            'category'       => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'created_at'     => ['required', 'date'],
            'description'    => ['nullable', 'string', 'max:255'],
            'periodicity'    => ['required_if:is_recurring_income,true', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'  => 'El monto es obligatorio.',
            'concept.required' => 'El concepto es obligatorio.',
            'concept.max'      => 'El concepto no debe exceder 50 caracteres.',
        ];
    }
}
```

---

## Routes

- All route URLs use **kebab-case with hyphens**
  - ✅ `recurring-incomes`, `bank-cards`, `toggle-status`
- Use **named routes** always, following the same kebab-case pattern
- Resource routes for standard CRUD
- Additional action routes follow the pattern: `{resource}/{action}` (e.g., `incomes/massive-delete`, `loans/get-matches`)

```php
// ✅ Correct
Route::resource('incomes', IncomeController::class)->middleware('auth');
Route::post('incomes/massive-delete', [IncomeController::class, 'massiveDelete'])->name('incomes.massive-delete');
Route::post('incomes/get-matches', [IncomeController::class, 'getMatches'])->name('incomes.get-matches');
Route::get('recurring-incomes/toggle-status/{recurring_income}', [RecurringIncomeController::class, 'toggleStatus'])->name('recurring-incomes.toggle-status');
```

---

## Vue 3 frontend

### General rules

- **Prefer** `<script setup>` with Composition API for new code
- Use `defineProps` and `defineEmits` with type annotations
- Keep components focused — if a component exceeds ~200 lines, split it
- Use Element Plus components for all UI: forms, tables, dialogs, buttons, inputs, selects, date pickers, etc.
- Never build custom form inputs, modals, or tables when Element Plus already provides them
- All user-facing text in Spanish

### Component naming

- **Pages:** `PascalCase` in `resources/js/Pages/{Module}/`
  - `resources/js/Pages/Income/Index.vue`
  - `resources/js/Pages/Income/Create.vue`
- **Reusable components:** `resources/js/Components/{Category}/`
  - `resources/js/Components/MyComponents/NotificationsCenter.vue`
  - `resources/js/Components/MyComponents/NotificationCard.vue`
  - `resources/js/Components/Dashboard/TotalBalance.vue`

### Layout structure

```
AppLayout.vue
├── AppMenu.vue           ← unified sidebar (desktop) + drawer (mobile)
│   └── uses ElMenu, ElDrawer, ElPopover from Element Plus
├── AppTopbar.vue         ← top bar (hamburger, logo, notifications, calendar)
└── <slot />              ← page content
```

Menu items are defined in a single array (`menuItems`) inside `AppMenu.vue` — add or remove items only there.

### Inertia patterns

- Use `useForm()` from `@inertiajs/vue3` for all forms
- Use `router.post()`, `router.put()`, `router.delete()` for mutations
- Use `route()` helper (via Ziggy) for all URLs
- Use `$inertia.get()` or `router.get()` for navigation

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
import { ElMessage } from 'element-plus'

const form = useForm({
  amount: null,
  concept: '',
  category: '',
  payment_method: '',
  created_at: '',
  description: '',
  is_recurring_income: false,
  periodicity: null,
})

function submit() {
  form.post(route('incomes.store'), {
    onSuccess: () => ElMessage.success('Ingreso registrado correctamente.'),
    onError: () => ElMessage.error('Error al registrar el ingreso.'),
  })
}
</script>
```

### Element Plus usage

- Use `el-form` + `el-form-item` for complex forms with `:model` and `:rules`
- Use `el-input`, `el-select`, `el-option`, `el-date-picker` for inputs
- Use `el-tabs`, `el-tab-pane` for tabbed interfaces
- Use `el-tag` for labels/tags
- Use `el-checkbox` for checkboxes
- Use `el-tooltip` for tooltips
- Use `el-table` + `el-table-column` for data tables
- Use `el-dialog` for modals — never custom overlays
- Use `ElMessage` for toast notifications (success, error, warning, info)
- Use `ElMessageBox` for confirmation dialogs before destructive actions

---

## UI/UX Style Guide — Stripe/Notion

This section defines the complete design system for the application. **Do not use values outside this palette** unless explicitly noted.

### 1. Design philosophy

- **Densidad de datos con respiración visual**: mucho contenido numérico (saldos, movimientos, gráficas), pero con espaciado generoso entre bloques.
- **Jerarquía por peso tipográfico y color, no por decoración**: evitar bordes gruesos, sombras pesadas o gradientes.
- **Un acento, usado con disciplina**: el color primario (`#296A6B`) se reserva para acciones primarias, elementos activos y navegación — nunca como fondo decorativo.
- **Colores semánticos siempre consistentes**: verde = ingreso/positivo, rojo = gasto/negativo, ámbar = pendiente/alerta. Crítico en una app financiera.

### 2. Color palette

#### 2.1 Primary (Teal) — based on `#296A6B`

| Token | Hex | Usage |
|---|---|---|
| `--color-primary-50` | `#EEF5F5` | Subtle hover backgrounds, active badge backgrounds |
| `--color-primary-100` | `#D7E8E8` | Selection backgrounds, table row hover |
| `--color-primary-200` | `#B0D1D1` | Subtle active borders |
| `--color-primary-300` | `#84B7B8` | Secondary icons on light backgrounds |
| `--color-primary-400` | `#5B9C9D` | Hover states for secondary elements |
| `--color-primary-500` | `#357F80` | Primary button hover |
| `--color-primary-600` | `#296A6B` | **Base** — primary buttons, links, active sidebar item |
| `--color-primary-700` | `#205254` | Active/pressed primary button |
| `--color-primary-800` | `#173B3C` | Text on light brand backgrounds |
| `--color-primary-900` | `#0F2728` | Dark sidebar background, max-contrast text |

#### 2.2 Warm accent — contrast with teal

| Token | Hex | Usage |
|---|---|---|
| `--color-accent-100` | `#FBE9DE` | Background for tip/onboarding cards |
| `--color-accent-400` | `#E8946B` | Decorative icons, chart accents |
| `--color-accent-600` | `#D9743F` | Occasional emphasis text (use sparingly) |

#### 2.3 Semantic colors (financial) — fixed, non-negotiable

| Meaning | Token | Hex | Light background |
|---|---|---|---|
| Income / positive | `--color-success-600` | `#2F9E5B` | `--color-success-50` `#EAF7EF` |
| Expense / negative | `--color-danger-600` | `#D64545` | `--color-danger-50` `#FCEAEA` |
| Pending / warning | `--color-warning-600` | `#D99A2B` | `--color-warning-50` `#FBF3E2` |
| Informational | `--color-info-600` | `#3B7DD8` | `--color-info-50` `#EAF1FB` |

Hover/active variants (darken 8-10%):
- `success-700`: `#247A47`
- `danger-700`: `#B23636`
- `warning-700`: `#B47F1F`
- `info-700`: `#2E63AD`

#### 2.4 Gray scale (cool gray, subtle teal tint)

| Token | Hex | Usage |
|---|---|---|
| `--color-gray-50` | `#F7F9F9` | App body background |
| `--color-gray-100` | `#EDF1F1` | Alternate card backgrounds, disabled input backgrounds |
| `--color-gray-200` | `#DCE3E3` | Card borders, dividers |
| `--color-gray-300` | `#C2CDCD` | Normal input borders |
| `--color-gray-400` | `#9AA8A8` | Placeholder text, inactive icons |
| `--color-gray-500` | `#728282` | Secondary text / labels |
| `--color-gray-600` | `#566363` | Default body text |
| `--color-gray-700` | `#3F4949` | Subtitles, table headers |
| `--color-gray-800` | `#2B3232` | Title text |
| `--color-gray-900` | `#1A1F1F` | Max emphasis text, main headings |

#### 2.5 White & surfaces

| Token | Hex | Usage |
|---|---|---|
| `--color-white` | `#FFFFFF` | Cards, modals, light-mode sidebar, header |
| `--color-bg` | `#F7F9F9` | General background behind cards |

### 3. Typography

```css
--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
--font-mono: 'JetBrains Mono', 'IBM Plex Mono', monospace;
```

- **`--font-sans` (Inter)**: all UI text — labels, paragraphs, buttons, navigation, headings.
- **`--font-mono`**: **exclusively** for monetary figures, amounts in tables, transaction IDs, dates in data format. This is key to the Stripe style — financial numbers in monospace align perfectly in columns and communicate precision.

#### Type scale

| Token | Size / line-height | Weight | Usage |
|---|---|---|---|
| `--text-xs` | 12px / 16px | 500 | Badge labels, metadata, timestamps |
| `--text-sm` | 13px / 20px | 400 | Secondary text, helper text |
| `--text-base` | 14px / 22px | 400 | Body text, table content |
| `--text-md` | 16px / 24px | 500 | Highlighted body text, form labels |
| `--text-lg` | 18px / 26px | 600 | Section subtitles, card titles |
| `--text-xl` | 22px / 30px | 600 | Page titles |
| `--text-2xl` | 28px / 36px | 700 | Main dashboard figure (e.g., total balance) |
| `--text-3xl` | 36px / 44px | 700 | Hero number (use sparingly) |

**Key Stripe/Notion rule**: use **font-weight** for hierarchy before size. Common to see `text-base` with `font-weight: 600` for an important label instead of increasing size.

### 4. Spacing, radii & shadows

#### 4.1 Spacing scale (4px system)

```css
--space-1: 4px;   --space-2: 8px;   --space-3: 12px;
--space-4: 16px;  --space-5: 20px;  --space-6: 24px;
--space-8: 32px;  --space-10: 40px; --space-12: 48px;
--space-16: 64px;
```

- Card inner padding: `--space-6` (24px)
- Dashboard card gap: `--space-5` (20px)
- Table cell padding: `--space-3` vertical, `--space-4` horizontal

#### 4.2 Border radii

```css
--radius-sm: 6px;    /* badges, small inputs */
--radius-md: 8px;    /* buttons, inputs */
--radius-lg: 12px;   /* cards */
--radius-xl: 16px;   /* modals, large floating panels */
--radius-full: 9999px; /* status pills, avatars */
```

```css
--border-width: 1px;
--border-color: var(--color-gray-200);
--border-color-strong: var(--color-gray-300);
```

#### 4.3 Shadows (subtle — key to the style)

```css
--shadow-xs: 0 1px 2px rgba(26, 31, 31, 0.04);
--shadow-sm: 0 1px 3px rgba(26, 31, 31, 0.06), 0 1px 2px rgba(26, 31, 31, 0.04);
--shadow-md: 0 4px 8px rgba(26, 31, 31, 0.06), 0 2px 4px rgba(26, 31, 31, 0.04);
--shadow-lg: 0 8px 16px rgba(26, 31, 31, 0.08), 0 2px 4px rgba(26, 31, 31, 0.04);
--shadow-focus: 0 0 0 3px rgba(41, 106, 107, 0.2);
```

Never use `box-shadow` larger than `--shadow-lg`. Dashboard cards use `--shadow-sm` by default and `--shadow-md` on hover if interactive.

### 5. Component specifications

#### 5.1 Sidebar navigation

- Background: `--color-primary-900` (`#0F2728`)
- Inactive text: light gray (`~#9CACAC`) for legibility on dark
- Active item: background `rgba(255,255,255,0.08)`, white text, 3px left indicator bar in `--color-primary-400`
- Icons: outline (not filled), 20px, same color as item text
- Width: 240px expanded, 64px collapsed

#### 5.2 Buttons

| Variant | Background | Text | Border | Hover |
|---|---|---|---|---|
| Primary | `--color-primary-600` | white | none | `--color-primary-500` |
| Secondary | white | `--color-primary-700` | 1px `--color-primary-200` | bg `--color-primary-50` |
| Tertiary/ghost | transparent | `--color-gray-700` | none | bg `--color-gray-100` |
| Danger | `--color-danger-600` | white | none | `--color-danger-700` |

- Radius: `--radius-md` (8px)
- Padding: `8px 16px` (sm), `10px 20px` (md), `12px 24px` (lg)
- Standard height: 36px (default), 32px (small), 40px (large)
- Label font-weight: 500

#### 5.3 Dashboard cards

```css
.card {
  background: var(--color-white);
  border: 1px solid var(--color-gray-200);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}
```

- Card title: `--text-sm`, `--color-gray-500`, `font-weight: 500`
- Main figure (e.g., "Saldo disponible"): `--text-2xl`, `font-mono`, `--color-gray-900`
- Percentage variation: small badge, green/red by sign, `--text-xs`

#### 5.4 Data tables (movements/transactions)

- Header: bg `--color-gray-50`, text `--text-xs` uppercase, `--color-gray-500`, `font-weight: 600`, `letter-spacing: 0.03em`
- Rows: bottom border `1px solid --color-gray-200`, no vertical borders
- Row hover: bg `--color-gray-50`
- Amount column: right-aligned, `font-mono`, color by sign:
  - Income: `--color-success-600`, prefix `+`
  - Expense: `--color-danger-600`, prefix `-`
- Row height: 48px minimum

#### 5.5 Status badges / pills

Pills (`--radius-full`), light semantic background + text in 700 variant:

```css
.badge-success { background: var(--color-success-50); color: var(--color-success-700); }
.badge-danger  { background: var(--color-danger-50);  color: var(--color-danger-700); }
.badge-warning { background: var(--color-warning-50); color: var(--color-warning-700); }
.badge-info    { background: var(--color-info-50);    color: var(--color-info-700); }
```

- Padding: `2px 10px`, `--text-xs`, `font-weight: 600`
- Examples: "Pagado" (success), "Vencido" (danger), "Pendiente" (warning), "Recurrente" (info)

#### 5.6 Inputs & forms

- Background: white
- Border: `1px solid --color-gray-300`
- Radius: `--radius-md`
- Height: 36px
- Focus: border `--color-primary-600` + `box-shadow: var(--shadow-focus)`
- Placeholder: `--color-gray-400`
- Label above input: `--text-sm`, `--color-gray-700`, `font-weight: 500`, bottom margin `--space-2`

#### 5.7 Charts

Series palette for charts (income vs expenses, categories), in priority order:

1. `--color-primary-600` (`#296A6B`) — main series
2. `--color-accent-400` (`#E8946B`) — secondary/contrast series
3. `--color-info-600` (`#3B7DD8`)
4. `--color-warning-600` (`#D99A2B`)
5. `--color-gray-400` (`#9AA8A8`) — neutral/other

For income-vs-expense charts specifically, **always** use success/danger instead of this general palette to maintain financial semantics.

### 6. CSS tokens consolidated

```css
:root {
  /* Primary */
  --color-primary-50: #EEF5F5;
  --color-primary-100: #D7E8E8;
  --color-primary-200: #B0D1D1;
  --color-primary-300: #84B7B8;
  --color-primary-400: #5B9C9D;
  --color-primary-500: #357F80;
  --color-primary-600: #296A6B;
  --color-primary-700: #205254;
  --color-primary-800: #173B3C;
  --color-primary-900: #0F2728;

  /* Warm accent */
  --color-accent-100: #FBE9DE;
  --color-accent-400: #E8946B;
  --color-accent-600: #D9743F;

  /* Semantic */
  --color-success-50: #EAF7EF;
  --color-success-600: #2F9E5B;
  --color-success-700: #247A47;
  --color-danger-50: #FCEAEA;
  --color-danger-600: #D64545;
  --color-danger-700: #B23636;
  --color-warning-50: #FBF3E2;
  --color-warning-600: #D99A2B;
  --color-warning-700: #B47F1F;
  --color-info-50: #EAF1FB;
  --color-info-600: #3B7DD8;
  --color-info-700: #2E63AD;

  /* Grays */
  --color-gray-50: #F7F9F9;
  --color-gray-100: #EDF1F1;
  --color-gray-200: #DCE3E3;
  --color-gray-300: #C2CDCD;
  --color-gray-400: #9AA8A8;
  --color-gray-500: #728282;
  --color-gray-600: #566363;
  --color-gray-700: #3F4949;
  --color-gray-800: #2B3232;
  --color-gray-900: #1A1F1F;

  --color-white: #FFFFFF;
  --color-bg: var(--color-gray-50);

  /* Typography */
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-mono: 'JetBrains Mono', 'IBM Plex Mono', monospace;

  /* Radii */
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-xs: 0 1px 2px rgba(26, 31, 31, 0.04);
  --shadow-sm: 0 1px 3px rgba(26, 31, 31, 0.06), 0 1px 2px rgba(26, 31, 31, 0.04);
  --shadow-md: 0 4px 8px rgba(26, 31, 31, 0.06), 0 2px 4px rgba(26, 31, 31, 0.04);
  --shadow-lg: 0 8px 16px rgba(26, 31, 31, 0.08), 0 2px 4px rgba(26, 31, 31, 0.04);
  --shadow-focus: 0 0 0 3px rgba(41, 106, 107, 0.2);
}
```

### 7. Element Plus theming (SCSS)

Override Element Plus internal CSS variables (`--el-*`) in a global `element-theme.scss` file imported **before** Element Plus component styles:

```scss
:root {
  // Primary
  --el-color-primary: var(--color-primary-600);
  --el-color-primary-light-3: var(--color-primary-400);
  --el-color-primary-light-5: var(--color-primary-300);
  --el-color-primary-light-7: var(--color-primary-200);
  --el-color-primary-light-8: var(--color-primary-100);
  --el-color-primary-light-9: var(--color-primary-50);
  --el-color-primary-dark-2: var(--color-primary-700);

  // Semantic mapped to Element Plus
  --el-color-success: var(--color-success-600);
  --el-color-success-light-9: var(--color-success-50);
  --el-color-danger: var(--color-danger-600);
  --el-color-danger-light-9: var(--color-danger-50);
  --el-color-warning: var(--color-warning-600);
  --el-color-warning-light-9: var(--color-warning-50);
  --el-color-info: var(--color-info-600);
  --el-color-info-light-9: var(--color-info-50);

  // Typography & text
  --el-font-family: var(--font-sans);
  --el-text-color-primary: var(--color-gray-900);
  --el-text-color-regular: var(--color-gray-600);
  --el-text-color-secondary: var(--color-gray-500);
  --el-text-color-placeholder: var(--color-gray-400);

  // Borders
  --el-border-color: var(--color-gray-200);
  --el-border-color-light: var(--color-gray-100);
  --el-border-radius-base: var(--radius-md);

  // Background
  --el-bg-color: var(--color-white);
  --el-bg-color-page: var(--color-gray-50);
  --el-fill-color-light: var(--color-gray-50);
}
```

### 8. Quick application rules (checklist)

1. **Never** use the primary color for expenses or errors — only for navigation/actions.
2. **Always** render monetary amounts with `--font-mono` and tabular figures (`font-feature-settings: 'tnum' 1`).
3. **Always** use green/red (success/danger) for income/expense — never any other combination.
4. Dark sidebar (`primary-900`) + content on `gray-50`/white — do not invert this relationship.
5. Shadows max `--shadow-lg`; prefer `--shadow-sm` in 90% of cases.
6. Radii of 8-12px on almost everything; avoid fully square or overly rounded corners (>16px) except pills.
7. Warm accent (`accent-400`/`accent-600`) used on max 1-2 elements per view — never as an extensive background color.
8. Visual hierarchy: first font-weight, then size, then color — in that priority order.

---

## User-scoped data pattern

All financial data belongs to a specific user. Every query must be scoped:

```php
// In controllers/services
Income::where('user_id', auth()->id())->latest('id')->paginate(50);

// When creating
Income::create($data + ['user_id' => auth()->id()]);
```

---

## Total money management

The `users.total_money` column tracks the user's global balance. It is mutated on:
- **Income creation**: increment by amount
- **Income update**: decrement old amount, increment new amount
- **Income deletion**: decrement by amount
- **Outcome creation**: decrement by amount
- **Loan payment (automatic)**: increment or decrement based on loan type

This logic should be extracted into a `TotalMoneyService` for consistency.

---

## Massive operations pattern

Several controllers support bulk operations via JSON request bodies:

```php
public function massiveDelete(Request $request)
{
    $request->validate(['ids' => 'required|array']);
    Income::whereIn('id', $request->ids)
        ->where('user_id', auth()->id())
        ->delete();

    return to_route('incomes.index');
}
```

---

## Calendar integration

When creating incomes/outcomes flagged as recurring, calendar events are generated with date ranges based on periodicity (`Todos los días`, `Semanal`, `Mensual`, `Anual`). A scheduled Artisan command (`calendar:process-scheduled`) processes these daily.

---

## Loan system

Loans support:
- **Types:** `Otorgado` (given) / `Recibido` (received)
- **Interest:** Configurable profitability with compound/simple modes
- **Payments:** Track amount, interest, capital, remaining balance
- **Automatic mode:** Creates Income/Outcome records for interest on each payment
- **External view:** Unauthenticated access via encrypted ID

---

## What to avoid

- ❌ Business logic inside controllers — extract to Actions/Services
- ❌ `$request->validate()` inside controllers — use Form Requests for new code
- ❌ `$guarded = []` in models — always define `$fillable`
- ❌ Raw queries when Eloquent scopes can do the job
- ❌ Custom CSS when Tailwind utility classes suffice
- ❌ Custom modals, form inputs, or tables when Element Plus provides them
- ❌ English in user-facing text — all UI must be in Spanish
- ❌ Inline business logic duplication — extract shared logic to Services
- ❌ Spanish in code (variable names, method names, comments) — code is in English
