<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <form @submit.prevent="save">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900">{{ $t('Preventive Maintenance') }}</h1>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mt-6 shadow sm:rounded-lg">
                    <loading :status="loading"/>
                    <div class="bg-white md:grid md:grid-cols-3 md:gap-6 px-4 py-5 sm:p-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $t('Preventive Maintenance') }}</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                {{ $t('details and customization') }}.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-3 gap-6">
                                
                                <!-- ol-span-3 | form fields | start -->
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Organization') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input-select
                                            id="department"
                                            v-model="ticket.department_id"
                                            :options="departmentList"
                                            option-label="name"
                                            @change="onDepartmentDropDownChange($event)"
                                            required
                                        />
                                    </div>
                                </div>

                                
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Main Locations') }}</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input-select
                                                id="department_locations"
                                                v-model="ticket.department_location_id"
                                                :options="departmentLocationList"
                                                option-label="location_name"
                                                @change="onDepartmentLocationChange()"
                                                required
                                            />
                                        </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Sub Locations') }}</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input-select
                                                id="department_sub_location_id"
                                                v-model="ticket.department_sub_location_id"
                                                :options="departmentSubLocationList"
                                                option-label="location_name"
                                                @change="onDepartmentSubLocationChange()"
                                                required
                                            />
                                        </div>
                                </div>


                                <div class="col-span-3">

                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('1 Preventive Maintenance') }}</label>

                                        <div class="mt-1 relative">
                                            <input
                                                class="datepicker_1_of_ticky"
                                                placeholder="yy-mm-dd"
                                                

                                                id="prev_maint_1_date"
                                                v-model="ticket.prev_maint_1_date"
                                                data-model="prev_maint_1_date"
                                                type="text"
                                            />

                                            <span class="ml-2">
                                                <span>Done</span>
                                                <input                                                    
                                                    id="prev_maint_1_done"
                                                    v-model="ticket.prev_maint_1_done"
                                                    type="checkbox"                                                    
                                                />
                                            </span>
                                        </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('2 Preventive Maintenance') }}</label>
                                        <div class="mt-1 relative">
                                            <input
                                                class="datepicker_1_of_ticky"
                                                placeholder="yy-mm-dd"
                                                

                                                id="prev_maint_2_date"
                                                v-model="ticket.prev_maint_2_date"
                                                data-model="prev_maint_2_date"
                                                type="text"
                                            />

                                            <span class="ml-2">
                                                <span>Done</span>
                                                <input                                                    
                                                    id="prev_maint_2_done"
                                                    v-model="ticket.prev_maint_2_done"                  
                                                    type="checkbox"                                                    
                                                />
                                            </span>
                                        </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('3 Preventive Maintenance') }}</label>
                                        <div class="mt-1 relative">
                                            <input
                                                class="datepicker_1_of_ticky"
                                                placeholder="yy-mm-dd"
                                                

                                                id="prev_maint_3_date"
                                                v-model="ticket.prev_maint_3_date"
                                                data-model="prev_maint_3_date"
                                                type="text"
                                            />

                                            <span class="ml-2">
                                                <span>Done</span>
                                                <input                                                    
                                                    id="prev_maint_3_done"
                                                    v-model="ticket.prev_maint_3_done"                  
                                                    type="checkbox"                                                    
                                                />
                                            </span>
                                        </div>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('4 Preventive Maintenance') }}</label>
                                        <div class="mt-1 relative">
                                            <input
                                                class="datepicker_1_of_ticky"
                                                placeholder="yy-mm-dd"
                                                

                                                id="prev_maint_4_date"
                                                v-model="ticket.prev_maint_4_date"
                                                data-model="prev_maint_4_date"
                                                type="text"
                                            />

                                            <span class="ml-2">
                                                <span>Done</span>
                                                <input                                                    
                                                    id="prev_maint_4_done"
                                                    v-model="ticket.prev_maint_4_done"                  
                                                    type="checkbox"                                                    
                                                />
                                            </span>
                                        </div>
                                </div>
                                <!-- ol-span-3 | form fields | end -->

                            </div>

                        </div>
                    </div>
                    <div class="bg-gray-100 text-right px-4 py-3 sm:px-6">
                        <div class="inline-flex">
                            <router-link
                                class="btn btn-secondary shadow-sm rounded-md mr-4"
                                to="/dashboard/admin/labels"
                            >
                                {{ $t('Cancel') }}
                            </router-link>
                            <button
                                class="btn btn-green shadow-sm rounded-md"
                                type="submit"
                            >
                                {{ $t('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
</template>


<script>
export default {
    name: "new",
    metaInfo() {
        return {
            title: this.$i18n.t('Preventive Maintenance')
        }
    },
    data() {
        return {
            loading: false,
            label: {
                name: null,
                color: '#2196F3',
            },

            ticket: {
                department_id: null,
                sub_department_id:null,
                department_location_id:null,
                department_sub_location_id:null,
                
                prev_maint_1_date:null,
                prev_maint_1_done:false,

                prev_maint_2_date:null,
                prev_maint_2_done:false,

                prev_maint_3_date:null,
                prev_maint_3_done:false,

                prev_maint_4_date:null,
                prev_maint_4_done:false,
            },
            allDepartment: [],
            departmentList: [],
            departmentLocationList:[],
            departmentSubLocationList:[],
        }
    },
    mounted() {
        this.getFilters();
        this.setDateFormate();
    },    
    methods: {
        setDateFormate(){
            const self = this;
            $( ".datepicker_1_of_ticky" ).attr('readonly', 'readonly').datepicker({
                onSelect: function(dateText, inst) {
                    let value = $(this).val();
                    // console.log('a--new date is:',value, self.ticket);
                    
                    let model = $(this).data('model');

                    if(model != undefined && model != null){
                        // eval('self.'+model+' = '+value);
                        // self[model] = value;
                        // self.ticket.prev_maint_1_date = value;
                        self.ticket[model] = value;
                    }

                }
            });
            $( ".datepicker_1_of_ticky" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
        },
        getDepartmentDetail(department_id){            
            for(let x in this.allDepartment){
                if(department_id == this.allDepartment[x].id){
                    return this.allDepartment[x];
                }
            }
            return {};
        },
        save(){
            const self = this;

            console.log('save ticket-', self.ticket);
            // console.log('getPreventiveMaintenanceDateOnChangeOfDD', self.ticket);

            axios.post('api/dashboard/preventive-maintenance/save', self.ticket).then(function (response) {
                // console.log('response.data', response.data.success, response.data.data);
                if(response.data.success){
                    self.$notify({
                        title: self.$i18n.t('Success').toString(),
                        text: self.$i18n.t('Data saved').toString(),
                        type: 'success'
                    });
                }
                else{
                    let eMessage = (response.data.message != undefined) ? response.data.message : 'Something went wrong';
                    self.$notify({
                        title: self.$i18n.t('Error').toString(),
                        text: eMessage.toString(),
                        type: 'warning'
                    });
                }
            }).catch(function () {
            })
        },
        getPreventiveMaintenanceDateOnChangeOfDD($event){
            const self = this;

            // console.log('getPreventiveMaintenanceDateOnChangeOfDD', self.ticket);

            axios.get('api/dashboard/preventive-maintenance/fetch', {
                params: self.ticket
            }).then(function (response) {
                // console.log('response.data', response.data.success, response.data.data);
                if(response.data.success){
                    if(response.data.data != null){                        
                        self.onValueUpdate('found', response.data.data);
                    }else{
                        self.onValueUpdate('not-found', null);
                    }
                }
            }).catch(function () {
            })

        },
        onValueUpdate(calledWhen, data){
            const self = this;
            console.log('in onValueUpdate--1', self.ticket);
            if(calledWhen == 'found'){
                self.ticket.prev_maint_1_done = data.prev_maint_1_done;
                self.ticket.prev_maint_2_done = data.prev_maint_2_done;
                self.ticket.prev_maint_3_done = data.prev_maint_3_done;
                self.ticket.prev_maint_4_done = data.prev_maint_4_done;

                self.ticket.prev_maint_1_date = data.prev_maint_1_date;
                self.ticket.prev_maint_2_date = data.prev_maint_2_date;
                self.ticket.prev_maint_3_date = data.prev_maint_3_date;
                self.ticket.prev_maint_4_date = data.prev_maint_4_date;
            }
            else{
                self.ticket.prev_maint_1_done = false;
                self.ticket.prev_maint_2_done = false;
                self.ticket.prev_maint_3_done = false;
                self.ticket.prev_maint_4_done = false;

                self.ticket.prev_maint_1_date = '';
                self.ticket.prev_maint_2_date = '';
                self.ticket.prev_maint_3_date = '';
                self.ticket.prev_maint_4_date = '';
            }
            
        },
        onDepartmentSubLocationChange($event){
            // console.log('onDepartmentSubLocationChange');
            this.getPreventiveMaintenanceDateOnChangeOfDD();
        },
        onDepartmentLocationChange($event){
            this.ticket.department_sub_location_id = null;

            let newSubLocation = [];
            if(this.ticket.department_location_id != null && this.departmentLocationList.length > 0){
                for(let x in this.departmentLocationList){
                    if(this.ticket.department_location_id ==  this.departmentLocationList[x].id){
                        newSubLocation = this.departmentLocationList[x].sub_locations;
                    }
                }
            }
            this.departmentSubLocationList = newSubLocation;

            // console.log('onDepartmentLocationChange, newSubLocation:',newSubLocation);
            // this.getPreventiveMaintenanceDateOnChangeOfDD();
        },
        onDepartmentDropDownChange($event){
            if(this.ticket.department_id != null){
                let tempDepartment = this.getDepartmentDetail(this.ticket.department_id);

                
                if(tempDepartment.departmentLocation != undefined && tempDepartment.departmentLocation != null){
                    this.departmentLocationList = tempDepartment.departmentLocation;
                }
                
                this.ticket.department_location_id = null;
                this.ticket.department_sub_location_id = null;
                this.departmentSubLocationList = [];
                
                // console.log('onDepartmentDropDownChange');

                // this.getPreventiveMaintenanceDateOnChangeOfDD();
            }
        },
        onlyParentDepartments(){
            let parentsDepartments = [];
            for(let x in this.allDepartment){
                if(this.allDepartment[x].parent_id < 1){
                    parentsDepartments.push(this.allDepartment[x]);
                }
            }
            return parentsDepartments;
        },
        getFilters() {
            const self = this;
            axios.get('api/dashboard/tickets/filters').then(function (response) {
                self.allDepartment = response.data.departments;
                self.departmentList = self.onlyParentDepartments();
            }).catch(function () {
            })
        },
        saveLabel() {
            
        },


    }//end of methods
}//end of class
</script>