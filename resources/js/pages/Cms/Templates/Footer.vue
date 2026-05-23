<script setup lang="ts">
defineProps<{
    content: {
        company_name?: string;
        address?: string;
        email?: string;
        phone?: string;
        facebook_url?: string;
        linkedin_url?: string;
        copyright_text?: string;
    };
}>();
</script>

<template>
    <footer class="border-t bg-card">
        <div class="mx-auto max-w-6xl px-4 py-10 md:py-12">
            <div class="grid gap-8 md:grid-cols-3">
                <!-- Company Info -->
                <div v-if="content?.company_name">
                    <h3 class="mb-3 text-lg font-semibold">{{ content.company_name }}</h3>
                    <p v-if="content?.address" class="text-sm text-muted-foreground">{{ content.address }}</p>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="mb-3 text-lg font-semibold">Contact</h3>
                    <div class="space-y-1 text-sm text-muted-foreground">
                        <p v-if="content?.email">{{ content.email }}</p>
                        <p v-if="content?.phone">{{ content.phone }}</p>
                    </div>
                </div>

                <!-- Social -->
                <div v-if="content?.facebook_url || content?.linkedin_url">
                    <h3 class="mb-3 text-lg font-semibold">Follow Us</h3>
                    <div class="flex gap-3">
                        <a
                            v-if="content?.facebook_url"
                            :href="content.facebook_url"
                            target="_blank"
                            class="text-sm text-muted-foreground underline hover:text-foreground"
                        >
                            Facebook
                        </a>
                        <a
                            v-if="content?.linkedin_url"
                            :href="content.linkedin_url"
                            target="_blank"
                            class="text-sm text-muted-foreground underline hover:text-foreground"
                        >
                            LinkedIn
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="content?.copyright_text" class="mt-8 border-t pt-6 text-center text-xs text-muted-foreground">
                {{ content.copyright_text }}
            </div>
        </div>
    </footer>
</template>
