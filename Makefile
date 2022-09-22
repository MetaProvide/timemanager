# This file is licensed under the Affero General Public License version 3 or
# later. See the LICENSE file.

app_name=timemanager
app_id=timemanager
build_directory=$(CURDIR)/build
temp_build_directory=$(build_directory)/temp
build_tools_directory=$(CURDIR)/build/tools

all: dev-setup build-js-production

release: npm-init build-js-production build-tarball
# Dev env management
dev-setup: clean-dev npm-init

# Dependencies
npm-init:
	npm ci

npm-update:
	npm update

# Building
build-js:
	npm run dev

build-js-production:
	npm run build

# Cleaning
clean-dev:
	rm -rf node_modules

build-tarball:
	rm -rf $(build_directory)
	mkdir -p $(temp_build_directory)
	rsync -a \
	--exclude=".git" \
	--exclude="node_modules" \
	--exclude="tests" \
	--exclude=".babelrc" \
	--exclude=".deployignore" \
	--exclude=".eslintrc" \
	--exclude=".gitignore" \
	--exclude=".prettierrc" \
	--exclude="Makefile" \
	--exclude="package-lock.json" \
	--exclude="package.json" \
	--exclude="rollup.config.js" \
	../$(app_name)/ $(temp_build_directory)/$(app_id)
	tar czf $(build_directory)/$(app_name).tar.gz \
		-C $(temp_build_directory) $(app_id)

