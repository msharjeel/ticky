<template>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <loading :status="stats.open_tickets == null"/>
            <a :href="getLinkOfTicketForStatus(1)">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $t('Open faults') }}
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ stats.open_tickets ? stats.open_tickets : 0 }}
                    </dd>
                </div>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <loading :status="stats.pending_tickets == null"/>
            <a :href="getLinkOfTicketForStatus(2)">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $t('Pending faults') }}
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ stats.pending_tickets ? stats.pending_tickets : 0 }}
                    </dd>
                </div>
            </a>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <loading :status="stats.solved_tickets == null"/>
            <a :href="getLinkOfTicketForStatus(4)">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $t('Solved faults') }}
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ stats.solved_tickets ? stats.solved_tickets : 0 }}
                    </dd>
                </div>
            </a>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <loading :status="stats.without_agent == null"/>
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ $t('Without assign agent') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ stats.without_agent ? stats.without_agent : 0 }}
                </dd>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "stats",
    data() {
        return {
            stats: {
                open_tickets: null,
                pending_tickets: null,
                solved_tickets: null,
                without_agent: null,
            }
        }
    },
    mounted() {
        this.getData();
        if(this.$store.state.user.notified_for_password_change != 1){
            window.location.href = "/account?msg_code=utp&msg=Update your temporary password.";
        }
    },
    methods: {
        getData() {
            const self = this;
            axios.get('api/dashboard/stats/count').then(function (response) {
                self.stats = response.data;
            });
        },
        getLinkOfTicketForStatus(status_id){
            let link = 'javascript:void(0);'
            if( this.$parent.userHasPermission_FromMixin("CAN_MANAGE_TICKETS") ){
                link = '/dashboard/tickets?statuses='+status_id;
            }
            else if( this.$parent.userHasPermission_FromMixin("CAN_MANAGE_MY_TICKETS") ){
                link = '/dashboard/tickets-my?statuses='+status_id;
            }
            return link;
        }
    },
}
</script>
