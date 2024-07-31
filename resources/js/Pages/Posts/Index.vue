<template>
    
    <AppLayout>
        <Container>
            <ul class="divide-y-">
                <li class="px-2 py-4" v-for="post in posts.data" :key="post.id">
                    <span class="font-bold text-lg">{{ post.title }}</span>
                </li>
            </ul>
            <Pagination :meta="posts.meta"/>
        </Container>
    </AppLayout>


</template>

<script setup>

import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps(['posts'])

</script>