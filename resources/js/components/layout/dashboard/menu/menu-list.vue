<template>
    <div>
        <div class="my-3 mx-4 uppercase text-gray-300 text-xs">
            {{ $t('General') }}
        </div>
        <menu-item
            :label="$t('Dashboard')"
            :mobile="mobile"
            icon="font-awesome.tachometer-alt-regular"
            to="/dashboard/home"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['CAN_MANAGE_TICKETS']"            
            :label="$t('Faults')"
            :mobile="mobile"
            icon="font-awesome.inbox-regular"
            to="/dashboard/tickets"
        ></menu-item>

        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['CAN_MANAGE_MY_TICKETS']"
            :label="$t('My Tickets (Faults)')"
            :mobile="mobile"
            icon="font-awesome.inbox-regular"
            to="/dashboard/tickets-my"
        ></menu-item>


        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.CannedReplyController']"
            :label="$t('Canned replies')"
            :mobile="mobile"
            icon="font-awesome.comments-alt-regular"
            to="/dashboard/canned-replies"
        ></menu-item>
        <div
            v-if="$store.state.permissions && (
                $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.UserController'] ||
                $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.UserRoleController'] ||
                $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.SettingController'] ||
                $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.LanguageController']
            )"
            class="my-3 mx-4 uppercase text-gray-300 text-xs"
        >
            {{ $t('Administration') }}
        </div>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.DepartmentController']"
            :label="$t('Organization')+'s'"
            :mobile="mobile"
            icon="font-awesome.users-class-regular"
            to="/dashboard/admin/organizations"
        ></menu-item>

        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.LabelController']"
            :label="$t('Preventive Maintenance')"
            :mobile="mobile"
            icon="font-awesome.tags-regular"
            to="/dashboard/admin/preventive-maintenance"
        ></menu-item>

        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['CAN_VIEW_REPORT']"
            :label="$t('Reports')"
            :mobile="mobile"
            icon="font-awesome.tags-regular"
            to="/dashboard/admin/reports/index"
        ></menu-item>

        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['CAN_VIEW_KPI_REPORT']"
            :label="$t('KPI Reports')"
            :mobile="mobile"
            icon="font-awesome.tags-regular"
            to="/dashboard/admin/reports/kpi"
        ></menu-item>


        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.LabelController']"
            :label="$t('Labels')"
            :mobile="mobile"
            icon="font-awesome.tags-regular"
            to="/dashboard/admin/labels"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.StatusController']"
            :label="$t('Fault status')"
            :mobile="mobile"
            icon="font-awesome.tasks-regular"
            to="/dashboard/admin/statuses"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['CAN_MANAGE_FAULT_TYPE']"
            :label="$t('Fault Types')"
            :mobile="mobile"
            icon="font-awesome.tags-regular"
            to="/dashboard/admin/fault_types"
        ></menu-item>

        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.PriorityController']"
            :label="$t('Priorities')"
            :mobile="mobile"
            icon="font-awesome.pennant-regular"
            to="/dashboard/admin/priorities"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.UserController']"
            :label="$t('Users')"
            :mobile="mobile"
            icon="font-awesome.users-regular"
            to="/dashboard/admin/users"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.UserRoleController']"
            :label="$t('User roles')"
            :mobile="mobile"
            icon="font-awesome.id-card-regular"
            to="/dashboard/admin/user-roles"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.SettingController']"
            :label="$t('Settings')"
            :mobile="mobile"
            icon="font-awesome.cog-regular"
            to="/dashboard/admin/settings"
        ></menu-item>
        <menu-item
            v-if="$store.state.permissions && $store.state.permissions['App.Http.Controllers.Api.Dashboard.Admin.LanguageController']"
            :label="$t('Translations')"
            :mobile="mobile"
            icon="font-awesome.language-regular"
            to="/dashboard/admin/languages"
        ></menu-item>
    </div>
</template>

<script>
import MenuItem from "@/components/layout/dashboard/menu/menu-item";

export default {
    name: "menu-list",
    components: {MenuItem},
    props: {
        mobile: {
            type: Boolean,
            required: false
        }
    }
}
</script>
