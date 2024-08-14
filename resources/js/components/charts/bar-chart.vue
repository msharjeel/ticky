<script>
import {Bar, mixins} from 'vue-chartjs'

const {reactiveProp} = mixins;

export default {
    name: 'bar-chart',
    extends: Bar,
    mixins: [reactiveProp],
    props: {
        chartData: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            options: {                
            }
        }
    },
    mounted() {
        this.renderChart(this.chartData, this.options);
    },
    methods: {
        update() {
            this.$data._chart.update();
        }
    }
}
</script>
<!-- 
Examples
https://codesandbox.io/s/yv8zrl0jwv?file=/src/App.vue 
-->