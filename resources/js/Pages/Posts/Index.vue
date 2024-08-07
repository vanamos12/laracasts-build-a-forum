<template>
    
    <AppLayout>
        <Container>
            <ul class="divide-y">
                <li class="" v-for="post in posts.data" :key="post.id">
                    <Link :href="route('posts.show', post.id)" class="group block px-2 py-4">
                        <span class="font-bold text-lg group-hover:text-indigo-500">{{ post.title }}</span>
                        <span class="block mt-1 text-sm text-gray-600">{{ formattedDate(post) }} ago by {{ post.user.name }}</span>
                    </Link>
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
import { relativeDate } from '@/Utilities/date';
import { Link } from '@inertiajs/vue3';

defineProps(['posts'])

const formattedDate = (post) => {
   return relativeDate(post.created_at);
}

</script>