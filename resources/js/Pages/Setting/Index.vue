<template>
    <AppLayout title="Configuraciones">
        <main class="px-2 md:px-10 pt-1 pb-16">
            <h1 class="font-bold my-3 ml-4 text-lg">Configuraciones</h1>

            <!-- Tabs -->
            <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
                <el-tab-pane label="Ingresos" name="1">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <p>General</p>
                        </div>
                    </template>
                    <General />
                </el-tab-pane>
                <el-tab-pane label="Ingresos recurrentes" name="2">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                            <p>Cuentas asociadas</p>
                        </div>
                    </template>
                    <BankCards :bankCards="bankCards" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>

<script>
import General from './Tabs/General.vue';
import BankCards from './Tabs/BankCards.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
data() {
    return {
        //tabs
        activeTab: '1',
        //tablas
        // filteredIncomes: this.incomes,
        // filteredRecurringIncomes: this.recurring_incomes,
    }
},
components:{
    PrimaryButton,
    AppLayout,
    BankCards,
    General,
},
props:{
    bankCards: Object
},
methods:{
    handleClick(tab) {
        // Obtén la URL actual
        const currentURL = new URL(window.location.href);

        // Agrega la variable currentTab=tab.props.name a la URL
        currentURL.searchParams.set('currentTab', tab.props.name);

        //revisa si existe una variable de paginacion
        const paginationURL = currentURL.searchParams.get('page');

        // Elimina el parámetro de paginación "page"
        currentURL.searchParams.delete('page');

        // Actualiza la URL sin recargar la página
        window.history.replaceState({}, document.title, currentURL.href);

        //actualiza la pagina si se cambio paginacion en alguna pestaña para no afectar la otra
        if ( paginationURL ) {
            location.reload();
        }
    }
},
mounted() {
    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    const currentTabFromURL = currentURL.searchParams.get('currentTab');

    if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
    }
},
}
</script>
