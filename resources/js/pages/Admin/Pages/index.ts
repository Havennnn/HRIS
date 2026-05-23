export interface PageIndexResource {
    id: number;
    title: string;
    slug: string;
    template: string;
    status: {
        label: string;
        variant: string;
    };
    status_value: number;
    created_at: string;
}

export interface PageEditResource {
    data: {
        id: number;
        title: string;
        slug: string;
        template: string;
        content: CmsBlock[] | Record<string, any>;
        meta: Record<string, string> | null;
        status_value: number;
        status: {
            label: string;
            variant: string;
        };
        created_at: string;
    };
}

export interface CmsBlock {
    id: string;
    type: string;
    data: Record<string, unknown>;
}

export interface BlockField {
    type: 'text' | 'textarea' | 'select' | 'file' | 'repeater';
    label: string;
    required?: boolean;
    placeholder?: string;
    options?: { value: string | number; label: string }[];
    fields?: Record<string, BlockField>;
}

export interface TemplateOption {
    value: string;
    label: string;
    description?: string | null;
    use_block_editor?: boolean;
}

export interface BlockConfig {
    label: string;
    icon: string;
    component: string;
    fields: Record<string, BlockField>;
}
