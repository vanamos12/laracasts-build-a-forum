<template>
    <AppLayout :title="post.title">
        <Container>
            <h1 class="text-2xl font-bold">{{ post.title }}</h1>
            <span class="block mt-1 text-sm text-gray-600">{{ formattedDate }} ago by {{ post.user.name }}</span>
            <article class="mt-6 ">
                <pre class="whitespace-pre-wrap font-sans">{{ post.body }}</pre>
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="commentIdBeingEdited ? updateComment() : addComment()" class="mt-4">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <TextArea  ref="commentTextAreaRef" id="body" rows="4" v-model="commentForm.body" placeholder="Speaks yoru mind spocks..."/>
                        <InputError :message="commentForm.errors.body" class="mt-1"/>
                    </div>

                    <PrimaryButton type="submit" class="mt-3" :disable="commentForm.processing" v-text="commentBeingEdit ? 'Update Comment' : 'Add Comment'"></PrimaryButton>
                    <SecondaryButton v-if="commentBeingEdit" @click="cancelEditComment" class="ml-2">Cancel</SecondaryButton>
                </form>
                <ul class="divide-y mt-4">
                    <li class="px-2 py-4" v-for="comment in comments.data" :key="comment.id">
                        <Comment @edit="editComment" @delete="deleteComment" :comment="comment" />
                    </li>
                </ul>

                <Pagination :meta="comments.meta" :only="['comments']"/>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>

import Comment from '@/Components/Comment.vue';
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/Utilities/Composables/useConfirm';
import { relativeDate } from '@/Utilities/date';
import { router,  useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps(['post', 'comments']);

const formattedDate = computed(() => relativeDate(props.post.created_at));

const commentForm = useForm({
    body: '',
})

const commentTextAreaRef = ref(null);

const commentIdBeingEdited = ref(null);

const commentBeingEdit = computed(() => props.comments.data.find(comment => comment.id === commentIdBeingEdited.value));

const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;
    commentForm.body = commentBeingEdit.value?.body;
    commentTextAreaRef.value?.focus();
}

const cancelEditComment = () => {
   commentIdBeingEdited.value = null 
   commentForm.reset();
}

const addComment = () => {
    commentForm.post(route('posts.comments.store', props.post.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    })
};

const {confirmation} = useConfirm();

const updateComment = async () => {
    if (! await confirmation('Are you sure you want to update this comment?')){
        commentTextAreaRef.value?.focus();
        return;
    }

   return commentForm.put(route('comments.update', {
    comment: commentBeingEdit.value,
    page: props.comments.meta.current_page
   }), {
    onSuccess: cancelEditComment,
    preserveScroll: true
   })
}


const deleteComment = async (commentId) => {
    if (! await confirmation('Are you sure you want to delete this comment?')){
        return;
    }

    return router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page }), {
        preserveScroll: true, 
    })
}
</script>