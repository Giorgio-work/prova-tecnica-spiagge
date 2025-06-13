import { cva, type VariantProps } from 'class-variance-authority';

export { default as Badge } from './Badge.vue';

export const badgeVariants = cva(
    'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    {
        variants: {
            variant: {
                default:
                    'border-transparent bg-primary text-primary-foreground shadow hover:bg-primary/80',
                secondary:
                    'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
                pending:
                    'border-transparent bg-yellow-700 text-yellow-700-foreground hover:bg-yellow-700/80',
                completed:
                    'border-transparent bg-green-700 text-green-700-foreground hover:bg-green-700/80',
                destructive:
                    'border-transparent bg-red-700 text-red-700-foreground shadow hover:bg-red-700/80',
                outline: 'text-foreground'
            }
        },
        defaultVariants: {
            variant: 'default'
        }
    }
);

export type BadgeVariants = VariantProps<typeof badgeVariants>
