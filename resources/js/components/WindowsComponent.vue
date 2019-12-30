<template>
    <section>
        <b-loading :is-full-page="true" :active.sync="isLoading" :can-cancel="true"></b-loading>
        <b-field>
            <b-select placeholder="Марка" expanded v-model="car_producer_id" @input="getCarModels">
                <option
                    v-for="option in car_producers"
                    :value="option.id"
                    :key="option.id">
                    {{ option.title }}
                </option>
            </b-select>
        </b-field>
        <b-field>
            <b-select placeholder="Модель" expanded v-model="car_model_id" @input="getCarBodies">
                <option
                    v-for="option in car_models"
                    :value="option.id"
                    :key="option.id">
                    {{ option.title }}
                </option>
            </b-select>
        </b-field>
        <b-field>
            <b-select placeholder="Кузов" expanded v-model="car_body_id">
                <option
                    v-for="option in car_bodies"
                    :value="option.id"
                    :key="option.id">
                    {{ option.title }}
                </option>
            </b-select>
        </b-field>
        <b-field>
            <b-select placeholder="Тип стекла" expanded v-model="window_type_id">
                <option
                    v-for="option in window_types"
                    :value="option.id"
                    :key="option.id">
                    {{ option.title }}
                </option>
            </b-select>
        </b-field>
        <b-field position="is-right" grouped>
            <div class="control"><b-button type="is-primary" v-on:click="getPick()">Подобрать</b-button></div>
        </b-field>
        <b-field grouped>
            <b-input v-model="window_title" placeholder="Номенклатура" expanded @keyup.enter.native="getSearch('window_title')"></b-input>
            <div class="control"><b-button type="is-primary" v-on:click="getSearch('window_title')">Поиск</b-button></div>
        </b-field>
        <b-field grouped>
            <b-input v-model="eurocode" placeholder="Еврокод" expanded @keyup.enter.native="getSearch('eurocode')"></b-input>
            <div class="control"><b-button type="is-primary" v-on:click="getSearch('eurocode')">Поиск</b-button></div>
        </b-field>
        <br><br>
        <b-pagination
            :total="total"
            :current.sync="current"
            :range-before="rangeBefore"
            :range-after="rangeAfter"
            :order="order"
            :size="size"
            :simple="isSimple"
            :rounded="isRounded"
            :per-page="perPage"
            :icon-prev="prevIcon"
            :icon-next="nextIcon"
            aria-next-label="След."
            aria-previous-label="Пред."
            aria-page-label="Стр."
            aria-current-label="Тек.">
        </b-pagination>
        <br><br>
        <b-table 
            :data="data"
            :columns="columns"
            :mobile-cards="hasMobileCards"
        ></b-table>
        <br><br>
        <b-pagination
            :total="total"
            :current.sync="current"
            :range-before="rangeBefore"
            :range-after="rangeAfter"
            :order="order"
            :size="size"
            :simple="isSimple"
            :rounded="isRounded"
            :per-page="perPage"
            :icon-prev="prevIcon"
            :icon-next="nextIcon"
            aria-next-label="След."
            aria-previous-label="Пред."
            aria-page-label="Стр."
            aria-current-label="Тек.">
        </b-pagination>
    </section>
</template>
<script>

    import Buefy from 'buefy'
    import 'buefy/dist/buefy.css'


    export default {
        mounted() {
            let query = {};
            this.getCarProducers();
            this.getWindowTypes();
            this.search(query);
        },
        methods:{
            getCarModels: function(){
                this.isLoading = true
                if(!this.car_producer_id){
                    this.car_bodies = [{id: 0, title: "Все"}];
                    this.car_models = [{id: 0, title: "Все"}];
                    this.car_model_id = 0;
                    this.car_body_id = 0;
                    this.isLoading = false
                    return false;
                }
                axios.post('/manager/windows/car_models', {car_producer_id: this.car_producer_id}).then(function(response){
                    this.car_models = response.data;
                    this.car_model_id = 0;
                    this.car_body_id = 0;
                    this.isLoading = false
                }.bind(this));
            },
            getCarBodies: function(){
                this.isLoading = true
                if(!this.car_model_id){
                    this.car_bodies = [{id: 0, title: "Все"}];
                    this.car_body_id = 0;
                    this.isLoading = false
                    return false;
                }
                axios.post('/manager/windows/car_bodies', {car_model_id: this.car_model_id}).then(function(response){
                    this.car_bodies = response.data;
                    this.car_body_id = 0;
                    this.isLoading = false
                }.bind(this));
            },
            getCarProducers: function(){
                axios.post('/manager/windows/car_producers').then(function(response){
                    this.car_producers = response.data;
                }.bind(this));
            },
            getWindowTypes: function(){
                axios.post('/manager/windows/window_types').then(function(response){
                    this.window_types = response.data;
                }.bind(this));
            },
            getSearch: function(model) {
                this.car_model_id = 0;
                this.car_body_id = 0;
                this.car_producer_id = 0;
                this.window_type_id = 0;
                this.car_bodies = [{id: 0, title: "Все"}];
                this.car_models = [{id: 0, title: "Все"}];
                let query = {
                    action: 'search'
                }
                if(model == "window_title") this.eurocode = "";
                if(model == "eurocode") this.window_title = "";
                query[model] = this[model];
                this.search(query)
            },
            getPick: function() {
                this.eurocode = "";
                this.window_title = "";
                let query = {
                    action: 'search',
                    car_producer_id: this.car_producer_id,
                    car_model_id: this.car_model_id,
                    car_body_id: this.car_body_id,
                    window_type_id: this.window_type_id
                }
                this.search(query)
            },
            search: function(query) {
                this.query = query;
                this.isLoading = true
                axios.get('/manager/windows/data', {params: query}).then(function(response){
                    let redata = [], i, j, str_tores;
                    for(i in response.data.data)
                    {

                        str_tores = "";
                        for(j in response.data.data[i].stores)
                        {
                            if(str_tores) str_tores += "<br>";
                            str_tores += "<nobr>" + response.data.data[i].stores[j][0] + ": " + response.data.data[i].stores[j][1] + "</nobr>";
                        }
                        response.data.data[i].stores = str_tores;
                        redata.push(response.data.data[i]);

                    }
                    // history.pushState(null, null, response.data.path)
                    this.data = response.data.data;
                    this.total = response.data.total;
                    this.current = response.data.current_page;
                    this.perPage = response.data.per_page;
                    this.isLoading = false;
                }.bind(this));
            }
        },
        watch: {
            // эта функция запускается при любом изменении вопроса
            current: function () {
                this.query.page = this.current;
                this.search(this.query);
            }
        },
        data() {

            const data = [];
            const colums = [
                {
                    field: 'window_producer_title',
                    label: 'Производитель',
                },
                {
                    field: 'car_producer_title',
                    label: 'Марка',
                },
                {
                    field: 'car_model_title',
                    label: 'Модель',
                },
                {
                    field: 'car_body_title',
                    label: 'Кузов',
                },
                {
                    field: 'window_type_title',
                    label: 'Тип стекла',
                },
                {
                    field: 'title',
                    label: 'Номенклатура',
                },
                {
                    field: 'eurocode',
                    label: 'Еврокод',
                },
                {
                    field: 'size',
                    label: 'Размер',
                },
                {
                    field: 'year',
                    label: 'Год',
                },
                {
                    field: 'char',
                    label: 'Характеристики',
                },
                {
                    field: 'spec',
                    label: 'Спецификация',
                },
                {
                    field: 'stores',
                    label: 'Склады',
                    renderHtml: true
                },
                {
                    field: 'price_opt',
                    label: 'Цена',
                },
                {
                    field: 'provider',
                    label: 'Поставщик',
                },
            ];

            return {
                data,
                isLoading: true,
                qeury: null,
                hasMobileCards: true,
                window_title: "",
                eurocode: "",
                car_producer_id: 0,
                car_body_id: 0,
                car_model_id: 0,
                window_type_id: 0,
                car_producers: [{id: 0, title: "Все"}],
                car_models: [{id: 0, title: "Все"}],
                window_types: [{id: 0, title: "Все"}],
                car_bodies: [{id: 0, title: "Все"}],
                total: 0,
                current: 1,
                perPage: 10,
                rangeBefore: 2,
                rangeAfter: 2,
                order: '',
                size: '',
                isSimple: false,
                isRounded: false,
                prevIcon: 'chevron-left',
                nextIcon: 'chevron-right',
                columns: colums
            }
        }
    }
</script>