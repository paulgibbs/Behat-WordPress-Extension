openssl aes-256-cbc -K $encrypted_a7d0b9fc4af0_key -iv $encrypted_a7d0b9fc4af0_iv -in bin/travis/publish-key.enc -out ~/.ssh/publish-key -d
chmod u=rw,og= ~/.ssh/publish-key
echo "Host github.com" >> ~/.ssh/config
echo "  IdentityFile ~/.ssh/publish-key" >> ~/.ssh/config
