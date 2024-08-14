<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <form @submit.prevent="save">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900">{{ $t('Reports') }}</h1>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="mt-6 shadow sm:rounded-lg"><!-- <-- Start of | Parents of reports main container  -->
                    
                    <section class="report_filters_section container">
                       
                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="report_from">{{ $t('From') }}</label>
                            <div class="mt-1 relative ">
                                <input type="text" name="report_from" id="report_from" v-model="filters.report_from" class="datepicker_1_of_ticky"/>
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="report_till">{{ $t('To') }}</label>
                            <div class="mt-1 relative ">
                                <input type="text" name="report_till" id="report_till" v-model="filters.report_till" class="datepicker_1_of_ticky" />
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="customer">{{ $t('Engineer') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="customer"
                                    v-model="filters.agent_id"
                                    :options="userList"
                                    option-label="name"
                                    required
                                    searchable
                                >
                                    <template v-slot:selectedOption="props">
                                        <template v-if="props && !props.anySelected">
                                            <span class="block truncate">{{ $t('Select an option') }}</span>
                                        </template>
                                        <template v-else>
                                            <div class="flex-shrink-0 inline-block">
                                                <img :src="props.option.avatar !== 'gravatar' ? props.option.avatar : props.option.gravatar" alt="Icon" class="w-5 h-auto rounded-full">
                                            </div>
                                            <span class="block truncate">{{ props.option.name }}</span>
                                        </template>
                                    </template>
                                    <template v-slot:selectOption="props">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 inline-block">
                                                <img :src="props.option.avatar !== 'gravatar' ? props.option.avatar : props.option.gravatar" alt="Icon" class="w-5 h-auto rounded-full">
                                            </div>
                                            <div :class="props.option.id === filters.agent_id ? 'font-semibold' : 'font-normal'" class="font-normal block truncate">
                                                {{ props.option.name }}
                                            </div>
                                        </div>
                                    </template>
                                </input-select>
                            </div>
                        </div>
                        
                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Organization') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="department"
                                    v-model="filters.department_id"
                                    :options="departmentList"
                                    option-label="name"
                                    @change="onDepartmentDropDownChange($event)"
                                    required
                                />
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Project') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="department"
                                    v-model="filters.sub_department_id"
                                    :options="subDepartmentsList"
                                    option-label="name"                                            
                                />
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="department">{{ $t('Main Locations') }}</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input-select
                                        id="department_locations"
                                        v-model="filters.department_location_id"
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
                                        v-model="filters.department_sub_location_id"
                                        :options="departmentSubLocationList"
                                        option-label="location_name"
                                        required
                                    />
                                </div>
                        </div>

                        <!-- Since we are showing all(4) categories of ticket status, so there isn't need of it -->
                        <div class="col-span-3" style="display: none;">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="status">{{ $t('Fault Status') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="status"
                                    v-model="filters.status_id"
                                    :options="statusList"
                                    option-label="name"
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="priority">{{ $t('Priority') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="priority"
                                    v-model="filters.priority_id"
                                    :options="priorityList"
                                    option-label="name"
                                    required
                                />
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="location_category_id">{{ $t('Location Category') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="location_category_id"
                                    v-model="filters.location_category_id"
                                    :options="locationCategoryList"
                                    option-label="name"
                                    required
                                />
                            </div>
                        </div>

                        <div class="col-span-3">
                            <label class="block text-sm font-medium leading-5 text-gray-700" for="ticket_type_id">{{ $t('Fault type') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input-select
                                    id="ticket_type_id"
                                    v-model="filters.ticket_type_id"
                                    :options="ticketTypesList"
                                    option-label="type"
                                    required
                                />
                            </div>
                        </div>


                    </section>

                    <div class="clearBothRufi"></div>

                    <div class="container">
                        <div class="btn btn-primary" @click="loadReport()">Load</div>
                        <div class="ml-2 btn btn-dark" @click="resetFilters()">Reset Filters</div>
                    </div>
                    
                    <div class="clearBothRufi"></div>
                    

                    <!-- Fault Summary Start -->
                    <section class="reportSection">
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mainBox">
                                    <h6>Fault Summary</h6>
                                    <div class="main">
                                        <div class="row ">
                                            
                                            <!-- Fault listing | start -->
                                            <div class="col-lg-6" v-for="report in reports_data">
                                                <div class="tkSummaryMain">
                                                    <div class="upperContent">
                                                        <span class="title">{{ report.label }}</span>
                                                        <span class="quantity" :style="'color:'+report.color+';'">{{ report.count }}</span>
                                                    </div>
                                                    <div class="progressBar">
                                                        <div class="progres" :style="'background-color:'+report.color+';width:'+report.percent+'%'">{{ report.percent }}%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Fault listing | end -->


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mainBox flow">
                                    <h6>Overview</h6>
                                    <div class="flowChart">
                                        <doughnut-chart ref="chart" :chart-data="d1ChartData" :height="325"></doughnut-chart>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Fault Summary End -->

                    <!-- Sites Summary Start -->
                    <section class="siteSummarySec" style="display: none;">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mainBox siteSummary">
                                    <h6>Sites Summary</h6>
                                    <div class="flowChart">
                                        <!-- <pie-chart ref="p1ChartRef" :chart-data="p1ChartData" :height="325"></pie-chart> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="mainBox">
                                    <h6>Request By Agent</h6>
                                    <div class="agentsTable">
                                        <table border="2" bordercolor="#f9f9f9" cellpadding="5" cellspacing="0" width="100%">
                                            <tr>
                                                <th>Name</th>
                                                <th>Open</th>
                                                <th>Completed</th>
                                                <th>OverDue</th>
                                            </tr>
                                            <tr>
                                                <td>Admin</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Salman</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Javier Montes Mancines</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Rachael</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Jorge Alejandro Montes Mancines</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            </tr>
                                            <tr>
                                                <td>Rachael</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            </tr>
                                            <tr>
                                                <td>Rachael</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            </tr>
                                            <tr>
                                                <td>Rachael</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                            </tr>
                                            <tr>
                                                <td>Rachael</td>
                                                <td>14</td>
                                                <td>5</td>
                                                <td>1</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Sites Summary End -->


                </div><!-- <-- End of | Parents of reports main container  -->
            </div>
        </form>
    </main>
</template>

<style scoped>
.clearBothRufi{ clear: both;width: 100%; height: 2px; margin: 0px 0px 20px 0px; }


section.report_filters_section {

}
section.report_filters_section .datepicker_1_of_ticky{
    width: 84%;
}

section.report_filters_section.container .col-span-3 {
    width: 29%;
    float: left;
    margin-right: 3%;
    margin-top: 14px;
}

.reportSection {
    background-color: #f8f7fc;
    padding: 10px;
}

.reportSection .mainBox {
    background-color: white;
    border-radius: 6px;
}

.reportSection .mainBox h6 {
    padding: 10px;
    border-bottom: 2p`x solid #f8f8f8;
    font-size: 16px;
}

.reportSection .mainBox .main {
    padding: 25px;
}

.reportSection .mainBox .tkSummaryMain {
    padding: 20px 15px;
    background-color: #f9f9f9;
    margin-bottom: 10px;
    border-radius: 10px;
}

.reportSection .mainBox .tkSummaryMain .upperContent {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.reportSection .mainBox .tkSummaryMain .upperContent span.title {
    font-size: 14px;
    font-weight: 600;
}

.reportSection .mainBox .tkSummaryMain .upperContent span.quantity {
    font-size: 26px;
    font-weight: 700;
}

.reportSection .mainBox .tkSummaryMain .progressBar {
    background-color: #e8ecef;
}

.reportSection .mainBox .tkSummaryMain .progressBar .progres {
    text-align: center;
    color: white;
    font-size: 14px;
    font-weight: 600;
}


.reportSection .flow {
    height: 100%;
}


/* Site Summary */

.siteSummarySec {
    background-color: #f8f7fc;
    padding: 10px;
}

.siteSummarySec .mainBox {
    background-color: white;
}

.siteSummarySec .mainBox h6 {
    padding: 10px;
    border-bottom: 2px solid #f8f8f8;
}

.siteSummarySec .mainBox .agentsTable {
    padding: 5px;
}

.siteSummarySec .mainBox .agentsTable table tr:nth-child(even) {
    background-color: #f7f8fa;
}

</style>

<script>
import DoughnutChart from "@/components/charts/doughnut-chart";
import PieChart      from "@/components/charts/pie-chart";

export default {
    name: "new",
    components: {DoughnutChart, PieChart},
    metaInfo() {
        return {
            title: this.$i18n.t('Reports')
        }
    },
    data() {
        return {
            loading: {
                form: true,
                file: false,
            },
            d1ChartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Faults report',
                        backgroundColor: [],
                        data: []
                    }
                ],
            },
            p1ChartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Sites Summary',
                        backgroundColor: [],
                        data: []
                    }
                ],
            },
            
            reports_data:[],

            filters:{
                report_from: '',
                report_till: '',
                
                agent_id: null,                

                department_id: null,
                sub_department_id:null,
                department_location_id:null,
                department_sub_location_id:null,
                priority_id: null,
                attachments: [],
                
                status_id: 1,

                location_category_id:null,
                ticket_type_id:null,
                
            },
            
            default_filters_str:null,

            allDepartment: [],
            departmentList: [],
            subDepartmentsList:[],
            departmentLocationList:[],
            departmentSubLocationList:[],
            locationCategoryList:[],
            ticketTypesList:[],

            priorityList: [],

            userList: [],
            statusList: [],
        }
    },
    computed: {
        datasets() {
            return this.d1ChartData.datasets[0].data
        }
    },
    watch: {
        datasets() {
            this.$refs.chart.update();
        }
    },
    mounted() {
       this.default_filters_str = JSON.stringify(this.filters);
       this.setDateFormate(); 
       this.getFilters();
       this.loadReport();
    },    
    methods: {
        setDateFormate(){
            const self = this;
            $( ".datepicker_1_of_ticky" ).attr('readonly', 'readonly').datepicker({
                // showButtonPanel: true,
                // closeText: '&times;',
                onSelect: function(dateText, inst) {
                    
                    // console.log('on date change-1, self.filters',self.filters);

                    let field_value = $(this).val();
                    let field_name = $(this).attr('name');

                    self.filters[ field_name ] = field_value;

                },
            });
            $( ".datepicker_1_of_ticky" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
        },
        mapObjData(obj1, objToMap){
            for(let x in objToMap){
                obj1[x] = objToMap[x]
            }
        },
        updateChatData(){
            const self = this;
            let dataSetBundle = {
                    label: 'Faults Report',
                    backgroundColor: [],
                    data: []
                };

            self.d1ChartData.labels = [];
            self.mapObjData(self.d1ChartData.datasets[0],dataSetBundle);
            
            // console.log('updateChatData', self.d1ChartData, self.reports_data);

            for(let x in self.reports_data){
                let gRow = self.reports_data[x];
                if(gRow.label != "All Faults"){
                    // console.log('423-gRow', x, gRow);

                    self.d1ChartData.labels.push( gRow.label );
                    
                    dataSetBundle.backgroundColor.push( gRow.color );
                    dataSetBundle.data.push( Math.floor(gRow.percent) );
                }
            }

            self.mapObjData(self.d1ChartData.datasets[0],dataSetBundle);

            // console.log('430-d1ChartData', self.d1ChartData, dataSetBundle);
        },
        resetFilters(){
            this.filters = JSON.parse( this.default_filters_str );
            this.loadReport();
        },
        loadReport(){
            const self = this;
            
            console.log('load report using filters:', self.filters);

            axios.get('api/tickets/reports', { params: self.filters} ).then(function (response) {
                self.reports_data = [];
                if(response.data.bool){
                    self.reports_data = response.data.data;
                }
                self.updateChatData();
                console.log('416-self.reports_data', self.reports_data);
            });
        },


        
        updateDepartmentName(){
            let tempDepartment = this.getDepartmentDetail(this.filters.department_id);
            
            if(tempDepartment.name != undefined && tempDepartment.name != null){
                this.department_name = tempDepartment.name;
            }
        },
        getDepartmentDetail(department_id){            
            for(let x in this.allDepartment){
                if(department_id == this.allDepartment[x].id){
                    return this.allDepartment[x];
                }
            }

            return {};
        },
        onDepartmentLocationChange($event){
            console.log('onDepartmentLocationChange', this.filters.department_location_id);

            this.filters.department_sub_location_id = null;

            let newSubLocation = [];
            if(this.filters.department_location_id != null && this.departmentLocationList.length > 0){
                for(let x in this.departmentLocationList){
                    if(this.filters.department_location_id ==  this.departmentLocationList[x].id){
                        newSubLocation = this.departmentLocationList[x].sub_locations;
                    }
                }
            }
            this.departmentSubLocationList = newSubLocation;
        },
        onDepartmentDropDownChange($event){
            if(this.filters.department_id != null){
                let tempDepartment = this.getDepartmentDetail(this.filters.department_id);

                if(tempDepartment.subDepartments != undefined && tempDepartment.subDepartments != null){
                    this.subDepartmentsList = tempDepartment.subDepartments;
                }

                if(tempDepartment.departmentLocation != undefined && tempDepartment.departmentLocation != null){
                    this.departmentLocationList = tempDepartment.departmentLocation;
                }

                this.filters.sub_department_id = null;
                
                this.filters.department_location_id = null;
                this.filters.department_sub_location_id = null;
                this.departmentSubLocationList = [];

                this.updateDepartmentName();
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
            let params = {};
            params.called_from = 'reports';
            axios.get('api/dashboard/tickets/filters',{params:params}).then(function (response) {
                self.statusList = response.data.statuses;
                self.userList = response.data.customers;
                self.priorityList = response.data.priorities;
                self.locationCategoryList = response.data.locationCategoryList;
                self.ticketTypesList = response.data.ticket_types;
                
                
                self.allDepartment = response.data.departments;
                if(self.$store.state.user.department_id > 0){
                    self.subDepartmentsList = self.allDepartment;                    
                }
                self.departmentList = self.onlyParentDepartments();
                self.onDepartmentDropDownChange(null);

                
                self.loading.form = false;

            }).catch(function () {
                self.loading.form = false;
            })
        },
    }//end of methods
}//end of class
</script>