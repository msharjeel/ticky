import Vue from 'vue';
import Cookies from 'js-cookie';
import VueRouter from 'vue-router';

import AuthLayout from '@/views/layout/auth';
import HelpdeskLayout from '@/views/layout/helpdesk';
import DashboardLayout from "@/views/layout/dashboard";

import AuthLoginPage from '@/views/pages/auth/login';
import AuthRegisterPage from '@/views/pages/auth/register';
import AuthRecoverPage from '@/views/pages/auth/recover';
import AuthResetPage from '@/views/pages/auth/reset';

import HelpdeskTicketsListPage from "@/views/pages/tickets/list";
import HelpdeskTicketsNewPage from "@/views/pages/tickets/new";
import HelpdeskTicketsManagePage from "@/views/pages/tickets/manage";

import DashboardHomePage from "@/views/pages/dashboard/home";

import DashboardCannedRepliesList from "@/views/pages/dashboard/canned-replies/list";
import DashboardCannedRepliesNew from "@/views/pages/dashboard/canned-replies/new";
import DashboardCannedRepliesEdit from "@/views/pages/dashboard/canned-replies/edit";

import DashboardTicketsList from "@/views/pages/dashboard/tickets/list";
import DashboardMyTicketsList from "@/views/pages/dashboard/tickets/list";
import DashboardTicketsNew from "@/views/pages/dashboard/tickets/new";
import DashboardTicketsManage from "@/views/pages/dashboard/tickets/manage";

import AdminDashboardDepartmentsLocationsOfDepartmentList from "@/views/pages/dashboard/admin/departments/locations_of_department_list";
import AdminDashboardDepartmentsLocationsOfDepartmentListClone from "@/views/pages/dashboard/admin/departments/locations_of_department_list_clone";

import AdminDashboardDepartmentsList from "@/views/pages/dashboard/admin/departments/list";
import AdminDashboardDepartmentsNew from "@/views/pages/dashboard/admin/departments/new";
import AdminDashboardDepartmentsEdit from "@/views/pages/dashboard/admin/departments/edit";

import AdminDashboardLabelsList from "@/views/pages/dashboard/admin/labels/list";
import AdminDashboardLabelsNew from "@/views/pages/dashboard/admin/labels/new";
import AdminDashboardLabelsEdit from "@/views/pages/dashboard/admin/labels/edit";

import AdminDashboardFaultTypesList from "@/views/pages/dashboard/admin/fault_types/list";
import AdminDashboardFaultTypesAddEdit from "@/views/pages/dashboard/admin/fault_types/addEdit";


import AdminDashboardPreventiveMaintenanceListAddEdit from "@/views/pages/dashboard/admin/preventive_maintenance/list-add-edit";
import AdminDashboardReportsIndex from "@/views/pages/dashboard/admin/reports/reports_index";
import AdminDashboardReportsKpiIndex from "@/views/pages/dashboard/admin/reports/reports_kpi_index";

import AdminDashboardStatusesList from "@/views/pages/dashboard/admin/statuses/list";
import AdminDashboardStatusesEdit from "@/views/pages/dashboard/admin/statuses/edit";

import AdminDashboardPrioritiesList from "@/views/pages/dashboard/admin/priorities/list";
import AdminDashboardPrioritiesEdit from "@/views/pages/dashboard/admin/priorities/edit";

import AdminDashboardUsersList from "@/views/pages/dashboard/admin/users/list";
import AdminDashboardUsersNew from "@/views/pages/dashboard/admin/users/new";
import AdminDashboardUsersEdit from "@/views/pages/dashboard/admin/users/edit";

import AdminDashboardUserRolesList from "@/views/pages/dashboard/admin/user-roles/list";
import AdminDashboardUserRolesNew from "@/views/pages/dashboard/admin/user-roles/new";
import AdminDashboardUserRolesEdit from "@/views/pages/dashboard/admin/user-roles/edit";

import AdminDashboardSettingsIndex from "@/views/pages/dashboard/admin/settings";
import AdminDashboardSettingsGeneral from "@/views/pages/dashboard/admin/settings/general";
import AdminDashboardSettingsSEO from "@/views/pages/dashboard/admin/settings/seo";
import AdminDashboardSettingsAppearance from "@/views/pages/dashboard/admin/settings/appearance";
import AdminDashboardSettingsLocalization from "@/views/pages/dashboard/admin/settings/localization";
import AdminDashboardSettingsAuthentication from "@/views/pages/dashboard/admin/settings/authentication";
import AdminDashboardSettingsOutgoingMail from "@/views/pages/dashboard/admin/settings/outgoing-mail";
import AdminDashboardSettingsLogging from "@/views/pages/dashboard/admin/settings/logging";
import AdminDashboardSettingsCaptcha from "@/views/pages/dashboard/admin/settings/captcha";

import AdminDashboardLanguagesList from "@/views/pages/dashboard/admin/language/list";
import AdminDashboardLanguagesNew from "@/views/pages/dashboard/admin/language/new";
import AdminDashboardLanguagesEdit from "@/views/pages/dashboard/admin/language/edit";

import AccountPage from "@/views/pages/account/account";

import DashboardNotFoundPage from "@/views/pages/dashboard/error/not-found";
import PageNotFoundPage from "@/views/pages/error/not-found";

Vue.use(VueRouter);

let routes = [
    {
        path: '/', redirect: '/auth/login',
    },
    {
        path: '/auth', component: AuthLayout, redirect: '/auth/login',
        children: [
            {path: 'login', component: AuthLoginPage, meta: {middleware: 'guest'}},
            {path: 'register', component: AuthRegisterPage, meta: {middleware: ['guest', 'register']}},
            {path: 'recover', component: AuthRecoverPage, meta: {middleware: 'guest'}},
            {path: 'reset/:token', component: AuthResetPage, meta: {middleware: 'guest'}},
        ]
    },
    {
        path: '/tickets', component: HelpdeskLayout, redirect: '/tickets/list',
        children: [
            {path: 'list', component: HelpdeskTicketsListPage, meta: {middleware: 'auth'}},
            {path: 'new', component: HelpdeskTicketsNewPage, meta: {middleware: 'auth'}},
            {path: ':uuid', component: HelpdeskTicketsManagePage, meta: {middleware: 'auth'}},
        ]
    },
    {
        path: '/dashboard', component: DashboardLayout, redirect: '/dashboard/home', meta: {dashboard_access: true},
        children: [
            {path: 'home', component: DashboardHomePage, meta: {middleware: 'auth', dashboard_access: true}},

            {path: 'tickets', component: DashboardTicketsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_MANAGE_TICKETS'}},
            {path: 'tickets-my', component: DashboardMyTicketsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_MANAGE_MY_TICKETS'}},
            
            {path: 'tickets/new', component: DashboardTicketsNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_CREATE_NEW_TICKET'}},
            {path: 'tickets/:uuid/manage', component: DashboardTicketsManage, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.TicketController'}},

            {path: 'canned-replies', component: DashboardCannedRepliesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.CannedReplyController'}},
            {path: 'canned-replies/new', component: DashboardCannedRepliesNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.CannedReplyController'}},
            {path: 'canned-replies/:id/edit', component: DashboardCannedRepliesEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.CannedReplyController'}},

            {path: 'admin/departments', component: AdminDashboardDepartmentsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            {path: 'admin/departments/new', component: AdminDashboardDepartmentsNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            {path: 'admin/departments/:id/edit', component: AdminDashboardDepartmentsEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},

            //Just for updating on front end(Departments-->Customers)
            {path: 'admin/organizations', component: AdminDashboardDepartmentsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            {path: 'admin/organizations/new', component: AdminDashboardDepartmentsNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            {path: 'admin/organizations/:id/edit', component: AdminDashboardDepartmentsEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            
            {path: 'admin/sub-organizations-of/:parent_id', component: AdminDashboardDepartmentsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.DepartmentController'}},
            {path: 'admin/locations-of-organization/:department_id', component: AdminDashboardDepartmentsLocationsOfDepartmentList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LocationsController'}},

            {path: 'admin/sub-locations-of-organization/:department_id/:parent_id', component: AdminDashboardDepartmentsLocationsOfDepartmentListClone, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LocationsController'}},
            


            {path: 'admin/labels', component: AdminDashboardLabelsList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LabelController'}},
            {path: 'admin/labels/new', component: AdminDashboardLabelsNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LabelController'}},
            {path: 'admin/labels/:id/edit', component: AdminDashboardLabelsEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LabelController'}},

            
            {path: 'admin/fault_types', component: AdminDashboardFaultTypesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_MANAGE_FAULT_TYPE'}},
            {path: 'admin/fault_types/addEdit', component: AdminDashboardFaultTypesAddEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_MANAGE_FAULT_TYPE'}},

            {path: 'admin/preventive-maintenance', component: AdminDashboardPreventiveMaintenanceListAddEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.PreventiveMaintenanceController'}},
            
            {path: 'admin/reports/index', component: AdminDashboardReportsIndex, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_VIEW_REPORT'}},
            {path: 'admin/reports/kpi', component: AdminDashboardReportsKpiIndex, meta: {middleware: 'auth', dashboard_access: true, controller: 'CAN_VIEW_KPI_REPORT'}},


            {path: 'admin/statuses', component: AdminDashboardStatusesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.StatusController'}},
            {path: 'admin/statuses/:id/edit', component: AdminDashboardStatusesEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.StatusController'}},

            {path: 'admin/priorities', component: AdminDashboardPrioritiesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.PriorityController'}},
            {path: 'admin/priorities/:id/edit', component: AdminDashboardPrioritiesEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.PriorityController'}},

            {path: 'admin/users', component: AdminDashboardUsersList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserController'}},
            {path: 'admin/users/new', component: AdminDashboardUsersNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserController'}},
            {path: 'admin/users/:id/edit', component: AdminDashboardUsersEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserController'}},

            {path: 'admin/user-roles', component: AdminDashboardUserRolesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserRoleController'}},
            {path: 'admin/user-roles/new', component: AdminDashboardUserRolesNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserRoleController'}},
            {path: 'admin/user-roles/:id/edit', component: AdminDashboardUserRolesEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.UserRoleController'}},

            {path: 'admin/settings', component: AdminDashboardSettingsIndex, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/general', component: AdminDashboardSettingsGeneral, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/seo', component: AdminDashboardSettingsSEO, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/appearance', component: AdminDashboardSettingsAppearance, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/localization', component: AdminDashboardSettingsLocalization, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/authentication', component: AdminDashboardSettingsAuthentication, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/outgoing-mail', component: AdminDashboardSettingsOutgoingMail, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/logging', component: AdminDashboardSettingsLogging, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},
            {path: 'admin/settings/captcha', component: AdminDashboardSettingsCaptcha, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.SettingController'}},

            {path: 'admin/languages', component: AdminDashboardLanguagesList, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LanguageController'}},
            {path: 'admin/languages/new', component: AdminDashboardLanguagesNew, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LanguageController'}},
            {path: 'admin/languages/:id/edit', component: AdminDashboardLanguagesEdit, meta: {middleware: 'auth', dashboard_access: true, controller: 'App.Http.Controllers.Api.Dashboard.Admin.LanguageController'}},

            {path: '*', component: DashboardNotFoundPage, meta: {middleware: 'auth', dashboard_access: true}},
        ]
    },
    {
        path: '/account', component: AccountPage, meta: {middleware: 'auth'},
    },
    {path: '*', component: PageNotFoundPage},
];

let router = new VueRouter({
    routes,
    mode: "history"
});

// Router auth middleware
router.beforeResolve((to, from, next) => {
    if (to.meta.middleware || to.meta.controller) {
        if (!localStorage.getItem('token') && to.meta.middleware.includes('auth')) {
            Cookies.set('intended_url', to.path);
            next('/auth/login');
        } else if (!window.app.register && to.meta.middleware.includes('register')) {
            next('/auth/login');
        } else if (localStorage.getItem('token') && to.meta.middleware.includes('guest')) {
            next('/tickets');
        } else if (localStorage.getItem('token') && to.meta.middleware.includes('auth')) {
            axios.post('api/auth/check', {controller: to.meta.controller})
            .then(function (response) {
                    // console.log("208-api/auth/check", response.data.access, response, to.meta);
                if (!response.data.access) {
                    Cookies.set('intended_url', to.path);
                    // next('/auth/login');
                    next('/dashboard/home');
                } else {
                    /*if (to.meta.dashboard_access && response.data.dashboard_access) {
                        next('/dashboard/home');
                    }
                    else */ if (to.meta.dashboard_access && !response.data.dashboard_access) {
                        next('/tickets');
                    }
                    else {
                        response.data.access ? next() : to.meta.controller ? next('/dashboard/home') : next('/auth/login');
                    }
                }
            })
            .catch((err) => {
              console.log("206-api/auth/check error", err);
              if( err.message == "CSRF token mismatch." ){
                window.location.reload();
              }
            });

        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
