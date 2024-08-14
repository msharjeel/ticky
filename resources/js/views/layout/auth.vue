<template>
    <div
        :style="{backgroundImage: 'url(' + $store.state.settings.background + ')'}"
        class="bg-center bg-no-repeat bg-cover items-center"
    >

        <a href="https://www.techdirectbh.com/" target="_blank" class="logo_bottom_right1">
            <img src="/images/tech_direct_logo1.png" width="180" >
        </a>

        <div class="container mx-auto">
            <div class="h-full min-h-screen flex">
                <div class="w-full m-auto">
                    <div class="flex justify-center">
                        <div class="w-full max-w-md bg-white shadow-md rounded p-10 my-10">
                            <router-link class="flex items-center justify-center mb-8" to="/">
                                <img :src="$store.state.settings.icon" alt="App icon" class="w-40"/>
                                <div 
                                    class="inline-block text-2xl text-gray-800 uppercase"
                                    v-if="$store.state.settings.icon == null || $store.state.settings.icon == ''"
                                >{{ $store.state.settings.name }}</div>
                            </router-link>
                            <router-view/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "auth",
    metaInfo() {
        return {
            titleTemplate: '%s - ' + this.$store.state.settings.name
        }
    },
}
</script>

<style scoped>
    .logo_bottom_right1{
        position: absolute;
        right: 20px;
        bottom: 20px;
    }
</style>