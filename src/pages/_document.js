import React from 'react';
import Document, { Html, Head, Main, NextScript } from 'next/document'

class AppDocument extends Document {
    static async getInitialProps(ctx) {
        const initialProps = await Document.getInitialProps(ctx);
        const langMatcher = ctx.pathname.match(/\/zh\-tw(\/{0,1})|\/en(\/{0,1})|\/ja(\/{0,1})/i);
        const lang = (langMatcher ? langMatcher[0] : 'zh-TW').replace(/^\//, '').replace(/\/$/, '');
        return { ...initialProps, lang: lang }
    }

    render() {
        return (
            <Html lang={this.props.lang}>
                <Head />
                <body>
                    <Main />
                    <NextScript />
                </body>
            </Html>
        )
    }
}

export default AppDocument