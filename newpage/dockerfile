# FROM node:10.15.2-slim AS build
FROM node:14.17.1-slim AS build
WORKDIR /src
COPY . .
#RUN npm config set registry http://registry.npm.taobao.org && npm install && npm run build
RUN yarn config set registry http://registry.npm.taobao.org && yarn config set sass_binary_site https://npm.taobao.org/mirrors/node-sass && yarn && while [ ! -d "node_modules" ]; do yarn; done && yarn build

FROM httpd:2.4
EXPOSE 80
#复制打包的文件
COPY --from=build /src/dist /usr/local/apache2/htdocs/