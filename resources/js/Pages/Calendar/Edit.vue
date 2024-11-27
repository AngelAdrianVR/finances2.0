<template>
  <AppLayout title="Editar recordatorio">
    <main class="px-3 md:px-16 py-8">
      <Back />
  
      <form @submit.prevent="update" class="rounded-xl border border-grayD9 lg:p-5 p-3 lg:w-2/3 xl:w-1/2 mx-auto mt-2 lg:grid lg:grid-cols-2 gap-3 shadow-lg">
        <div class="flex items-center justify-between col-span-full">
          <el-collapse accordion>
            <el-collapse-item v-if="remainType === 'Ingreso recurrente'" title="Ingreso recurrente" name="1">
              <template #title>
                <h1 class="text-primary font-bold text-base">{{ remainType }}</h1>
              </template>
              <div>
                Los ingresos recurrentes se registran automáticamente en el sistema.
                Estos corresponden a entradas periódicas, como sueldos, rentas u otros pagos regulares.
              </div>
            </el-collapse-item>
            <el-collapse-item v-else title="Gasto fijo" name="2">
              <template #title>
                <h1 class="text-primary font-bold text-base">{{ remainType }}</h1>
              </template>
              <div>
                Los gastos fijos se registran automáticamente en el sistema. 
                Estos corresponden a salidas periódicas, pago de servicios, alimentos y otros pagos regulares.
              </div>
            </el-collapse-item>
          </el-collapse>
          <div>
            <el-segmented @change="changeType" v-model="remainType" :options="options" block />
          </div>
        </div>

        <!-- form body -->
        <div class="col-span-full mb-3 lg:mb-0">
            <InputLabel v-if="remainType === 'Ingreso recurrente'" value="Nombre del ingreso*" class="ml-3 mb-1" />
            <InputLabel v-else value="Nombre del gasto*" class="ml-3 mb-1" />
            <el-input
                v-model="form.title"
                maxlength="80"
                :placeholder="remainType === 'Ingreso recurrente' ? 'Ej. Pago de nómina' : 'Ej. Compra de despensa'"
                show-word-limit
                type="text"
            />
            <InputError :message="form.errors.title" />
        </div>

        <div class="mb-3 lg:mb-0">
            <InputLabel value="Fecha de inicio*" class="ml-3 mb-1" />
            <el-date-picker
                class="!w-full"
                v-model="form.date"
                type="date"
                placeholder="Selecciona la fecha de inicio"
                :disabled-date="disabledDate"
            />
            <InputError :message="form.errors.date" />
        </div>

        <div class="mb-3 lg:mb-0">
            <InputLabel value="Monto*" class="ml-3 mb-1" />
            <el-input
              v-model="form.amount"
              placeholder="Ej. $2,000"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
            >
              <template #prepend>
                $
              </template>
            </el-input>
            <InputError :message="form.errors.amount" />
        </div>

        <div class="mb-3 lg:mb-0">
            <InputLabel value="Categoría" class="ml-3 mb-1" />
            <el-select class="!w-full" filterable v-model="form.category"
                placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option class="md:!w-[500px]" v-for="category in categories" :key="category" :label="category.label" :value="category.label">
                    <p class="flex items-center justify-between">
                        <span>{{ category.label }}</span>
                        <span class="text-xs text-gray-400">{{ category.description }}</span>
                    </p>
                </el-option>
            </el-select>
            <InputError :message="form.errors.category" />
        </div>

        <div class="mb-3 lg:mb-0">
            <InputLabel value="Método de pago" class="ml-3 mb-1" />
            <el-select class="!w-full" filterable v-model="form.payment_method"
                placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in paymentMethods" :key="item" :label="item" :value="item">
                    <p class="flex items-center justify-between">
                        <span>{{ item }}</span>
                    </p>
                </el-option>
            </el-select>
            <InputError :message="form.errors.payment_method" />
        </div>

        <div class="mb-3 lg:mb-0">
            <InputLabel value="Frecuencia*" class="ml-3 mb-1" />
            <el-select class="!w-full" filterable v-model="form.periodicity"
                placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in periodicities" :key="item" :label="item" :value="item">
                    <p class="flex items-center justify-between">
                        <span>{{ item }}</span>
                    </p>
                </el-option>
            </el-select>
            <InputError :message="form.errors.periodicity" />
        </div>

        <div class="col-span-full">
            <InputLabel value="Descripción" class="ml-3 mb-1" />
            <el-input
                v-model="form.description"
                maxlength="255"
                placeholder="Agrega una breve descripción (opcional)"
                show-word-limit
                type="textarea"
            />
            <InputError :message="form.errors.description" />
        </div>

        <div class="col-span-full space-x-4 text-right mt-7">
            <PrimaryButton :disabled="form.processing">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guardar cambios
            </PrimaryButton>
        </div>
      </form>      
    </main>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Back from "@/Components/MyComponents/Back.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      type: this.calendar.type,
      title: this.calendar.title,
      date: this.calendar.date,
      amount: this.calendar.amount,
      category: this.calendar.category,
      description: this.calendar.description,
      periodicity: this.calendar.periodicity,
      payment_method: this.calendar.payment_method,
      
    });

    return {
      form,
      remainType: this.calendar.type,
      options: [
        'Ingreso recurrente',
        'Gasto fijo'
      ],
      paymentMethods: [
        'Transferencia',
        'Depósito',
        'Cheque',
        'Efectivo'
      ],
      periodicities: [
        'Diario',
        'Cada 2 días',
        'Cada 3 días',
        'Semanal',
        'Quincenal',
        'Mensual',
        'Bimestral',
        'Trimestral',
        'Cuatrimestral',
        'Semestral',
        'Anual',
      ],
      categories: [
        {
          label: 'Nómina',
          description: '(Salarios o sueldos fijos)'
        },
        {
          label: 'Renta de propiedad',
          description: '(Ingresos por alquiler de propiedades)'
        },
        {
          label: 'Regalías',
          description: '(Pagos por derechos de autor, música, libros, etc.)'
        },
        {
          label: 'Dividendos',
          description: '(Ganancias por acciones o inversiones)'
        },
        {
          label: 'Pensión',
          description: '(Ingresos por jubilación o retiro)'
        },
        {
          label: 'Contratos de servicios',
          description: '(Mantenimiento o soporte técnico)'
        },
        {
          label: 'Arrendamientos de equipos',
          description: '(Maquinaria, vehículos, etc.)'
        },
        {
          label: 'Ingresos por franquicias',
          description: '(Pagos regulares de franquiciados)'
        },
        {
          label: 'Rendimientos financieros',
          description: '(Intereses por inversiones)'
        },
      ]
    };
  },
  components: {
    PrimaryButton,
    InputError,
    InputLabel,
    AppLayout,
    Checkbox,
    Back
  },
  props: {
    calendar: Object
  },
  methods: {
    update() {
      this.form.put(route("calendars.update", this.calendar.id), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "",
            type: "success",
          });
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    changeType(newVal) {
      this.remainType = newVal;
      this.form.type = newVal;
    }
  },
};
</script>

