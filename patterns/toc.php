<?php
/**
 * Title: Table of Contents
 * Slug: wickedevolutions/toc
 * Categories: hidden
 * Inserter: false
 */

$post = get_post();
if ( ! $post ) {
    return;
}

if ( (bool) get_post_meta( $post->ID, 'we_disable_toc', true ) ) {
    return;
}

$categories = get_the_category( $post->ID );
if ( $categories ) {
    foreach ( $categories as $cat ) {
        if ( (bool) get_term_meta( $cat->term_id, 'we_disable_toc', true ) ) {
            return;
        }
    }
}

// Cache headings globally so the pattern can render twice (desktop rail + mobile slot).
global $we_toc_headings;

if ( ! isset( $we_toc_headings ) ) {
    $blocks   = parse_blocks( $post->post_content );
    $we_toc_headings = [];
    $used_ids = [];

    if ( ! function_exists( 'we_toc_collect_headings' ) ) {
        function we_toc_collect_headings( $blocks, &$headings, &$used_ids ) {
            foreach ( $blocks as $block ) {
                if ( 'core/heading' === $block['blockName'] ) {
                    $level = $block['attrs']['level'] ?? 2;
                    if ( $level < 2 || $level > 3 ) {
                        continue;
                    }

                    $text = trim( wp_strip_all_tags( render_block( $block ) ) );
                    if ( '' === $text ) {
                        continue;
                    }

                    $anchor = $block['attrs']['anchor'] ?? '';
                    if ( '' === $anchor ) {
                        $anchor = sanitize_title( $text );
                    }

                    $orig   = $anchor;
                    $suffix = 2;
                    while ( in_array( $anchor, $used_ids, true ) && $suffix < 50 ) {
                        $anchor = $orig . '-' . $suffix;
                        $suffix++;
                    }
                    $used_ids[] = $anchor;

                    $headings[] = [
                        'level'  => (int) $level,
                        'text'   => $text,
                        'anchor' => $anchor,
                    ];
                }

                if ( ! empty( $block['innerBlocks'] ) ) {
                    we_toc_collect_headings( $block['innerBlocks'], $headings, $used_ids );
                }
            }
        }
    }

    we_toc_collect_headings( $blocks, $we_toc_headings, $used_ids );

    // Inject IDs into headings via early the_content filter (once only).
    if ( count( $we_toc_headings ) >= 2 ) {
        $captured = $we_toc_headings;
        add_filter( 'the_content', function ( $content ) use ( $captured ) {
            foreach ( $captured as $h ) {
                $tag     = 'h' . $h['level'];
                $text_re = preg_quote( $h['text'], '/' );
                $id      = esc_attr( $h['anchor'] );

                $pattern = '/(<' . $tag . ')(\s[^>]*)?(>)\s*' . $text_re . '\s*(<\/' . $tag . '>)/i';

                $content = preg_replace_callback( $pattern, function ( $m ) use ( $id ) {
                    $open  = $m[1];
                    $attrs = $m[2] ?? '';
                    $close_bracket = $m[3];
                    $end   = $m[4];

                    $full = $m[0];
                    $inner_start = strpos( $full, $close_bracket ) + 1;
                    $inner_end   = strrpos( $full, $end );
                    $text        = substr( $full, $inner_start, $inner_end - $inner_start );

                    $attrs = preg_replace( '/\sid=["\'][^"\']*["\']/', '', $attrs );

                    return $open . $attrs . ' id="' . $id . '"' . $close_bracket . trim( $text ) . $end;
                }, $content, 1 );
            }
            return $content;
        }, 5 );
    }
}

$headings = $we_toc_headings;

if ( count( $headings ) < 2 ) {
    return;
}
?>

<nav class="we-toc-nav" aria-label="Table of contents">
    <button type="button" class="we-toc-toggle" aria-expanded="false">
        <span class="we-toc-toggle-label">On this page</span>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <ul class="we-toc-list">
        <?php foreach ( $headings as $h ) : ?>
        <li class="we-toc-item we-toc-h<?php echo $h['level']; ?>">
            <a href="#<?php echo esc_attr( $h['anchor'] ); ?>"><?php echo esc_html( $h['text'] ); ?></a>
        </li>
        <?php endforeach; ?>
        <li class="we-toc-item we-toc-back">
            <a href="#">Back to top</a>
        </li>
    </ul>
</nav>
