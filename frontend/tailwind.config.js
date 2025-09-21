import tailwindcssAnimate from 'tailwindcss-animate'

const config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx}",
    "./components/**/*.{js,ts,jsx,tsx}",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  darkMode: "class",
  theme: {
    container: {
        center: true,
        padding: '2rem',
        screens: {
            '2xl': '1400px'
        }
    },
    extend: {
        colors: {
            border: 'hsl(var(--border))',
            input: 'hsl(var(--input))',
            ring: 'hsl(var(--ring))',
            background: 'hsl(var(--background))',
            foreground: 'hsl(var(--foreground))',
            primary: {
                DEFAULT: 'hsl(var(--primary))',
                foreground: 'hsl(var(--primary-foreground))',
                glow: 'hsl(var(--primary-glow))'
            },
            secondary: {
                DEFAULT: 'hsl(var(--secondary))',
                foreground: 'hsl(var(--secondary-foreground))'
            },
            destructive: {
                DEFAULT: 'hsl(var(--destructive))',
                foreground: 'hsl(var(--destructive-foreground))'
            },
            muted: {
                DEFAULT: 'hsl(var(--muted))',
                foreground: 'hsl(var(--muted-foreground))'
            },
            accent: {
                DEFAULT: 'hsl(var(--accent))',
                foreground: 'hsl(var(--accent-foreground))'
            },
            popover: {
                DEFAULT: 'hsl(var(--popover))',
                foreground: 'hsl(var(--popover-foreground))'
            },
            card: {
                DEFAULT: 'hsl(var(--card))',
                foreground: 'hsl(var(--card-foreground))'
            },
            sidebar: {
                DEFAULT: 'hsl(var(--sidebar-background))',
                foreground: 'hsl(var(--sidebar-foreground))',
                primary: 'hsl(var(--sidebar-primary))',
                'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
                accent: 'hsl(var(--sidebar-accent))',
                'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
                border: 'hsl(var(--sidebar-border))',
                ring: 'hsl(var(--sidebar-ring))'
            }
        },
        borderRadius: {
            lg: 'var(--radius)',
            md: 'calc(var(--radius) - 2px)',
            sm: 'calc(var(--radius) - 4px)'
        },
        fontFamily: {
            sans: ['var(--font-poppins)', 'Poppins', 'system-ui', 'sans-serif'],
            mono: ['var(--font-fira-code)', 'Fira Code', 'ui-monospace', 'monospace'],
        },
        backgroundImage: {
            'hero-gradient': 'var(--hero-gradient)',
            'feature-gradient': 'var(--feature-gradient)',
            'stats-gradient': 'var(--stats-gradient)'
        },
        keyframes: {
            'accordion-down': {
                from: {
                    height: '0'
                },
                to: {
                    height: 'var(--radix-accordion-content-height)'
                }
            },
            'accordion-up': {
                from: {
                    height: 'var(--radix-accordion-content-height)'
                },
                to: {
                    height: '0'
                }
            },
            'fade-in-up': {
                '0%': {
                    opacity: '0',
                    transform: 'translateY(30px)'
                },
                '100%': {
                    opacity: '1',
                    transform: 'translateY(0)'
                }
            },
            'scale-in': {
                '0%': {
                    opacity: '0',
                    transform: 'scale(0.9)'
                },
                '100%': {
                    opacity: '1',
                    transform: 'scale(1)'
                }
            },
            float: {
                '0%, 100%': {
                    transform: 'translateY(0px)'
                },
                '50%': {
                    transform: 'translateY(-10px)'
                }
            }
        },
        animation: {
            'accordion-down': 'accordion-down 0.2s ease-out',
            'accordion-up': 'accordion-up 0.2s ease-out',
            'fade-in-up': 'fade-in-up 0.6s ease-out',
            'scale-in': 'scale-in 0.5s ease-out',
            float: 'float 3s ease-in-out infinite'
        }
    }
  },
    plugins: [tailwindcssAnimate]
}

export default config